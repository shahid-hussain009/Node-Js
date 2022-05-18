# Node-Js-Installation

### To Start Mongodb just use
```js
1- mongod --directoryperdb --dbpath="E:\\env\data\db" --logpath="E:\\env\data\log\mongo.log" 
--logappend  --install
2- net start mongodb
```
### Use debugger in NodeJs(1)
#### Create launch.json file and following code 
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


