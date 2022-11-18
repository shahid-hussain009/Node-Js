# Node-Js-Installation

### To Start Mongodb just use
```js
1- mongod --directoryperdb --dbpath="E:\\env\data\db" --logpath="E:\\env\data\log\mongo.log" 
--logappend  --install
2- net start mongodb
```
### Date and Time in Nodejs
```js
const dateTime = new Date();
dateTime.setHours(dateTime.getHours() + 5);
```
### Use debugger in NodeJs(1)
#### Create launch.json file and add fallowing code 
```json

{
    "version": "0.2.0",
    "configurations": [
        {
            "type": "node",
            "request": "launch",
            "name": "nodemon",
            "runtimeExecutable": "node",
            "envFile": "${workspaceFolder}\\app\\.env",
            "program": "${workspaceFolder}\\app\\bin\\www",
            "restart": true,
            "console": "integratedTerminal",
            "internalConsoleOptions": "neverOpen",
            "timeout": 40000,
            "skipFiles": [
                "node_modules/**/*.js"
            ]
        },
       
        {
            "type": "node",
            "request": "launch",
            "name": "Launch Program",
            "program": "${workspaceFolder}\\app\\bin\\www",
            "envFile": "${workspaceFolder}/.envnodemon"
        },
        {
            "type": "node",
            "request": "attach",
            "name": "Node: Nodemon",
            "processId": "${command:PickProcess}",
            "restart": true,
            "protocol": "inspector",
        },
        {
            "type": "node",
            "request": "launch",
            "name": "nodemonmac",
            "runtimeExecutable": "nodemon",
            "envFile": "${workspaceFolder}/.env",
            "program": "${workspaceFolder}/app/bin/www",
            "restart": true,
            "console": "integratedTerminal",
            "internalConsoleOptions": "neverOpen",
            "skipFiles": [
                "node_modules/**/*.js"
            ]
        }
    ]
  }
  ```
  
  ### Orignal Response
```json
"version": 1,
    "startTimestamp": "2022-11-18T11:59:39.647+05:00",
    "endTimestamp": "2022-11-18T11:59:39.675+05:00",
    "outputs": {
        "TransactionID": [
            "03448585531150202211181159391",
            "03448585531150202211181159392",
            "03448585531150202211181159393",
            "03448585531150202211181159394"
        ],
        "Customer_MSISDN": "03448585531",
        "Onnet_Minutes": [
            "450",
            "1500",
            "3000",
            "50"
        ]
        "Response_CD": "00",
    }
```
### Response Map for above response
```json
let offerList = [];
        params.outputs.TransactionID.forEach((item, index) => {
            offerList[index] = {
                productLabel: params.outputs.Offer_Text[index],
                offerType: "recommended",
                discount: {
                    discountEnabled: false,
                    discountedDisplayPrice: "Rs. 0",
                },
            }
            if (!offerList[index].attributes) {
                offerList[index] = { ...offerList[index], attributes: [] };
            }
            //
            offerList[index].attributes.push(
                {
                    Onnet_Minutes: params.outputs.Onnet_Minutes[index],
                    allowanceValue: params.outputs.Onnet_Minutes[index],
                    discountedAllowanceValue: ""
		});
          
            // transaction id
            offerList[index] = {
                ...offerList[index],
                transactionID: params.outputs.TransactionID[index]
            };
        })
	

```


