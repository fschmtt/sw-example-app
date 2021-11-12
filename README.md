# Example Shopware 6 App

Just to getting to know the App system ;-)

## Requirements
* Docker

Have the [shopware/development](https://gitlab.shopware.com/shopware/6/product/development) project up and running.

## Installation

Clone this repository into `custom/apps/SwExampleApp`.

Copy the `.env.dist` file to `.env` and adjust it accordingly.

Install Composer dependencies by running `docker-compose run composer composer install`.

Start the App by running `docker-compose up -d`.

## Development

You can access the App via http://localhost:8181.

In your App's `manifest.xml` you’ll want to use:
```xml
    ...
    <setup>
        <registrationUrl>http://localhost:8181/registration/register</registrationUrl>
    </setup>
    ...
```

## Routes

|Route|Description|
|---|---|
|`GET /registration/register`||
|`POST /registration/confirm`||
|`POST /action-buttons/greet-customer`|Action button to greet customer|
|`POST /webhooks/greet-customer`|Webhook for `checkout.customer.registered` event|
|`GET /modules/greetings`|Main module for Shopware Administration|

## Registration Workflow (Setup)

### Example Registration Request
```http request
GET /registration/register?shop-id=GLblL0Q8YI7veaV5&shop-url=http://localhost:8000&timestamp=1634563302

Host: http://localhost:8181
Sw-User-Language: en-GB
Sw-Context-Language: 2fbb5fe2e29a4d70aa5854ce7ce3e20b
Sw-Version: 6.4.6.0
Shopware-App-Signature: e58d9b15962180fe013fc3414bfa1a0436017e2f77c7d33d3459c74f1eaf4bdb
```

### Example Registration Response
```json
{
    "proof": "833e639da5798c182786515db9aaf0ad7657ae15968030c45bebb2ed26e8309d",
    "secret": "18a53af4c55d2c4460937dab1054c338022fccbae9eb68da00e2d7c3e3c62b87",
    "confirmation_url": "http://localhost:8181/registration/confirm"
}
```

### Example Confirmation Request
```http request
POST /registration/confirm

Sw-User-Language: en-GB
Sw-Context-Language: 2fbb5fe2e29a4d70aa5854ce7ce3e20b
Sw-Version: 6.4.6.0
Shopware-Shop-Signature: b3a2cc3e9c622cf24579518a951b6d569eaf00970b46bb20b97bf8fb0fbdb2a1
Content-Type: application/json
Host: http://localhost:8181

{
    "apiKey":"SWIAWJNGBNZRQJHTVGKWZTLVTQ",
    "secretKey":"MWN0a3ppZXZSOWpLRUk1d0pKMUdxVDB4dkEzNVFOOWY3bjZaTEo",
    "timestamp":"1634563305",
    "shopUrl":"http:\/\/localhost:8000",
    "shopId":"GLblL0Q8YI7veaV5"
}
```
