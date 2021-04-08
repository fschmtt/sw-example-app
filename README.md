# Example Shopware 6 App

Just to getting to know the App system ;-)

## Requirements
* Docker
* Composer

Have the `shopware/development` project up and running.

## Installation

Clone this repository into `custom/apps/SwExampleApp`.

Install Composer dependencies by running `composer install`.

Start the App by running `docker-compose up -d`.

Connect the App by running `docker network connect --alias sw-example-app development_shopware swexampleapp_sw-example-app-nginx_1`.

## Development

You can access the App via http://localhost/ (default HTTP port `80`).

From within the Docker network the container is accessible by its alias `sw-example-app` i.e. http://sw-example-app/ (again default HTTP port `80`).

In your App's `manifest.xml` you’ll want to use:
```xml
    ...
    <setup>
        <registrationUrl>http://sw-example-app/registration</registrationUrl>
    </setup>
    ...
```
