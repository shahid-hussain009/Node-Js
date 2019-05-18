# Node-Js-Installation
```text 
Install Nodejs and Monodb
```
### To Start Mongodb just use
```text
1- mongod --directoryperdb --dbpath="E:\\env\data\db" --logpath="E:\\env\data\log\mongo.log" 
--logappend  --install
2- net start mongodb
```
### Install Express with .handlebars view engine
```text
express yourProjectName --hbs
```
### Install Express with .twig view engine
```text
express --view=twig yourProjectName
```
### Twig View Engine Syntax Example
```text
View Success Message

{% if success %}
	<div class="alert alert-success alert-dismissible">
		<button type="button" class="close" data-dismiss="alert">&times;</button>
		<strong>Success!</strong> {{success}}
	</div>
{% endif %}

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

