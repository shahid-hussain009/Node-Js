# Node-Js-Installation

### To Start Mongodb just use
```js
1- mongod --directoryperdb --dbpath="E:\\env\data\db" --logpath="E:\\env\data\log\mongo.log" 
--logappend  --install
2- net start mongodb
```
### Node js with Express npm
```js
npm init
npm i --save express
npm i --save nodemon -g
npm i --save body-parser
npm i --save express-messages express-session connect-flash express-validator
npm i --save passport passport-local bcryptjs
npm install --save mongoose-unique-validator
```
### Install Express with .handlebars view engine
```js
express yourProjectName --hbs
```
### Install Express with .twig view engine
```js
express --view=twig yourProjectName
```
### Npm commonds
```js
npm install --save-dev -D webpack webpack-cli css-loader style-loader extract-text-webpack-plugin@next
npm install jquery popper.js bootstrap
```
### Run NPM in Production Mode
```js
webpack --progress --mode=production
```
### Twig View Engine Syntax Example
```html
View Success Message

{% if success %}
	<div class="alert alert-success alert-dismissible">
		<button type="button" class="close" data-dismiss="alert">&times;</button>
		<strong>Success!</strong> {{success}}
	</div>
{% endif %}

View Validation Error

<div class="form-group">
      	<label for="exampleInputEmail1">Item Name</label>
      	<input type="text" name="item" class="form-control"  placeholder="Item" value="{{orderErr.item}}">
    	<p style="color:red">
    	{% if errors.item %}
      		{{errors.item.msg}}
    	{% endif %}
    	</p>
</div>

For Loop To Print Data

  {% set sn = 0 %}
{% for row in rows %}
<tr>
	<td>  {% set sn = sn + 1 %}
		{{ sn }}
	</td>
	<td>{{row.item}}</td>
	<td>
	<a class="btn btn-success btn-xs" href="/edit/{{row._id}}">Edit</a>
	<a class="btn btn-danger btn-xs" href="/delete/{{row._id}}">Delete</a>
	</td>
</tr>
{% endfor %}
```
### Basic CURD With Validation
### App js
```js
var createError = require('http-errors');
var express = require('express');
var path = require('path');
var cookieParser = require('cookie-parser');
var logger = require('morgan');
var expressValidator = require('express-validator');
var passport = require("passport");
var LocalStrategy = require('passport-local').Strategy;
var flash = require('connect-flash');
var session = require('express-session');


var mongoose = require('mongoose');



var indexRouter = require('./routes/index');
var usersRouter = require('./routes/users');
var loginRouter = require('./routes/login');
var registerRouter = require('./routes/register');

var app = express();


app.set('views', path.join(__dirname, 'views'));
app.set('view engine', 'twig');

app.use(expressValidator());

app.use(logger('dev'));
app.use(express.json());
app.use(express.urlencoded({ extended: false }));
app.use(cookieParser());
app.use(express.static(path.join(__dirname, 'public')));

app.use(session({
	secret: 'secret',
	saveUninitialized:true,
	resave: true
}));

app.use(flash());

app.use(passport.initialize());
app.use(passport.session());

app.use('/', indexRouter);
app.use('/users', usersRouter);
app.use('/login', loginRouter);
app.use('/register', registerRouter);


app.use((req, res, next) => {
	res.locals.success_msg = req.flash('success_msg');
	res.locals.error_msg = req.flash('error_msg');
	res.locals.error = req.flash('error');
	res.locals.user = req.user || null;
  	next();
})



app.use(function(req, res, next) {
  next(createError(404));
});



app.use(function(err, req, res, next) {
  res.locals.message = err.message;
  res.locals.error = req.app.get('env') === 'development' ? err : {};

  res.status(err.status || 500);
  res.render('error');
});

module.exports = app;



```
### Order Model
```js
var mongoose = require('mongoose');
// Mlab
mongoose.connect("mongodb://testmongo:username#@password.mlab.com:41133/nodemongo", { useNewUrlParser: true });
// Local
//mongoose.connect("mongodb://localhost:27017/nodeknowledge", { useNewUrlParser: true });

var conn = mongoose.connection;
var OrderSchema = mongoose.Schema({
  item: String,
  qty: Number,
  price: Number,
  total: Number
});

var OrderModel = mongoose.model('orders', OrderSchema);
module.exports = OrderModel;

```

### Index Route
```js
var express = require('express');
var Orders = require('../models/order');
const { check, validationResult } = require('express-validator/check');
const { matchedData, sanitize } = require('express-validator/filter');
var router = express.Router();

var query = Orders.find({});
/* GET home page. */
router.get('/', ensureAuthenticated, (req, res, next) => {
  query.exec(function(err, doc){
    if(err) throw err;
    res.render('index', { title: 'Add New Order', rows: doc });
  });
  
});

function ensureAuthenticated(req, res, next){
	if(req.isAuthenticated())
	{
		return next();
	}
	else
	{
		req.flash('error_msg', 'You are not Logged in');
		res.redirect('/login');
	}
}

router.post('/',
    [
        check('item', 'Item is required')
        .trim()
        .isLength({ min: 4 })
        .withMessage('Must be at least 4 chars long'),
        check('price', 'Price is required')
        .trim().isNumeric(),
        check('qty', 'Qty is required')
        .trim().isNumeric()
    ], 
    (req, res) => {
        const errors = validationResult(req);
        if(!errors.isEmpty())
	{
            const orderErr = matchedData(req);
            query.exec(function(err, doc)
	    {
              if(err) throw err;
              res.render('index', { title: 'Add New Order', rows: doc, errors: errors.mapped(),
              orderErr:orderErr });
            });
            
        }
	else
	{
          var newRecord = new Orders({
            item: req.body.item,
            price: req.body.price,
            qty: req.body.qty,
            total: parseFloat(req.body.price) * parseFloat(req.body.qty),
          })
          newRecord.save(function(err, doc)
	  {
            if(err) throw err;
            query.exec(function(err, doc)
	    {
              if(err) throw err;
              res.render('index', { title: 'Add New Order', rows: doc, message: 'Order submitted successfully' });
            })
          })
        }
       
})

router.get('/delete/:id', function(req, res, next){
  var del = Orders.deleteOne({_id: req.params.id });
    del.exec(function(err){
      if(err) throw err;
      res.redirect('/');
    })
})


router.get('/edit/:id', function(req, res, next){
  var ed = Orders.findById(req.params.id);
    ed.exec(function(err, doc){
      if(err) throw err;
      res.render('edit',{
        titele: 'Edit Order',
        doc: doc
      })
    })
   
})

router.post('/update', function(req, res, next){
  var update = Orders.updateOne(
    { _id: req.body.id},
    {
      item: req.body.item,
      price: req.body.price,
      qty: req.body.qty,
      total: parseFloat(req.body.price) * parseFloat(req.body.qty),
    });
  update.exec(function(err){
    if(err) throw err;
    res.redirect('/');
  }) 
})

module.exports = router;

```

