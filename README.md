# Shopware 6 App Example

Shopware 6 app based on the [Slim framework](https://www.slimframework.com/).

## Requirements
* Have a running Shopware 6 instance
* Docker

## Installation

Clone this repository into `custom/apps/SwExampleApp`.

Copy the `.env.dist` file to `.env` and adjust it accordingly.

Install Composer dependencies by running `docker-compose run composer composer install`.

Start the app server by running `docker-compose up -d`.

## Configuration

The app server is configured via the `.env` file.

| Environment variable | Type | Description |
|----------------------|------|-------------|
| `APP_NAME` | `string` | The technical name of the app. |
| `APP_SECRET` | `string` | The secret provided by the Shopware Account in the manufacturer's area. |
| `FRONTEND_URL` | `string` | The URL that modules for the Administration are accessible from. |
| `BACKEND_URL` | `string` | The URL that is used for registration and webhooks. |
| `VERIFY_SHOP_URL` | `bool` | Whether the provided `shop-url` should be verified upon registration. |

## Development

You can access the App via http://localhost:8592.

In your App's `manifest.xml` you’ll want to use:
```xml
    ...
    <setup>
        <registrationUrl>http://localhost:8592/registration/register</registrationUrl>
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

## Middlewares

### VerifyShopSignature
This middleware checks the authenticity of an incoming request, i.e. the request is signed properly.
For `GET` requests it looks up the query parameter `shopware-shop-signature`; for `POST` requests it looks up the HTTP header `shopware-shop-signature`.

The middleware stores the following attributes on the request:

| Attribute | Description |
| --- | --- |
| SHOP_ID | The ID of the shop that sent the request. |
| SHOP_URL | The URL of the shop that sent the request. |
| SHOP_SECRET | The secret (used for signing) of the shop that sent the request. |
| SHOP_API_KEY | The API key of the shop that sent the request. |
| SHOP_SECRET_KEY | The secret key of the shop that sent the request. |

The attributes can be accessed via `$request->getAttribute()`.

### VerifyAppSignature
tbd

### ShopwareVersion
tbd

## Registration Workflow (Setup)

### Example Registration Request
```http request
GET /registration/register?shop-id=GLblL0Q8YI7veaV5&shop-url=http://localhost:8000&timestamp=1634563302

Host: http://localhost:8592
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
    "confirmation_url": "http://localhost:8592/registration/confirm"
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
Host: http://localhost:8592

{
    "apiKey":"SWIAWJNGBNZRQJHTVGKWZTLVTQ",
    "secretKey":"MWN0a3ppZXZSOWpLRUk1d0pKMUdxVDB4dkEzNVFOOWY3bjZaTEo",
    "timestamp":"1634563305",
    "shopUrl":"http:\/\/localhost:8000",
    "shopId":"GLblL0Q8YI7veaV5"
}
```
