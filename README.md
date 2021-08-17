# Example Shopware 6 App

Just to getting to know the App system ;-)

## Requirements
* Docker

Have the [shopware/development](https://gitlab.shopware.com/shopware/6/product/development) project up and running.

## Installation

Clone this repository into `custom/apps/SwExampleApp`.

Install Composer dependencies by running `docker-compose run composer composer install`.

Start the App by running `docker-compose up -d`.

## Development

You can access the App via http://localhost:8181/.

In your App's `manifest.xml` you’ll want to use:
```xml
    ...
    <setup>
        <registrationUrl>http://localhost:8181/registration</registrationUrl>
    </setup>
    ...
```

## Routes

|Route|Description|
|---|---|
|`GET /registration`||
|`POST /registration/confirm`||
|`POST /customer/greet`|Webhook for `checkout.customer.registered` event|
|`GET /greetings/module`|Main module for Shopware Administration|
