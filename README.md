# **Multive** - PHP Web Framework

Used technologies: `PHP 7.3^, Slim 4, MySQL, dotenv, PHP-DI 6, VueJs, Axios`.

[![Software License][ico-license]](LICENSE.md)
[![Version](https://img.shields.io/packagist/v/roufy235/Multive?style=plastic)](https://packagist.org/packages/roufy235/multive)
![Code Size](https://img.shields.io/github/languages/code-size/roufy235/Multive?style=plastic)
![Downloads](https://img.shields.io/packagist/dm/roufy235/Multive?style=plastic)

[ico-license]: https://img.shields.io/github/license/roufy235/Multive?style=plastic



## Requirement

- [Composer](https://getcomposer.org/).
- [NodeJs](https://nodejs.org/).
- [GulpJs](https://gulpjs.com/).
- PHP 7.3+.
- MySQL/MariaDB.

## Quickstart guide

You can create a new project by running this command:
```bash
$ composer create-project roufy235/multive
```
* Install [Node.js](https://nodejs.org/en/) if you don't have it yet.
* Install `gulp-cli` if you don't have it yet.
```bash
$ npm install --global gulp-cli
```
* Run `npm install`
* Run `gulp` to run the default Gulp task

#### Configure your connection to MySQL Server:

By default, the FRAMEWORK use a MySQL Database.

You should check and edit this configuration in your `.env` file:

```
DB_HOST = '127.0.0.1'
DB_NAME = 'yourMySqlDatabase'
USER = 'yourMySqlUsername'
PASSWORD = 'yourMySqlPassword'
```

## :package: DEPENDENCIES:

### LIST OF REQUIRE DEPENDENCIES:

- [slim/slim](https://github.com/slimphp/Slim) Slim is a PHP micro framework that helps you quickly write simple yet powerful web applications and APIs.
- [slim/psr7](https://github.com/slimphp/Slim-Psr7) PSR-7 implementation for use with Slim 4.
- [php-di/php-di](https://php-di.org/) A PHP dependency injection container.
- [egulias/email-validator](https://github.com/egulias/EmailValidator) PHP Email address validator library.
- [phpmailer/phpmailer](https://github.com/PHPMailer/PHPMailer) PHPMailer - A full-featured email creation and transfer class for PHP
- [slim/php-view](https://github.com/slimphp/PHP-View) This is a renderer for rendering PHP view scripts into a PSR-7 Response object. It works well with Slim Framework 4.
- [vlucas/phpdotenv](https://github.com/vlucas/phpdotenv) Loads environment variables from `.env` to `getenv()`, `$_ENV` and `$_SERVER` automatically.



## Built On
* [Slim Framework](http://www.slimframework.com/) - Slim is a PHP micro framework that helps you quickly write simple yet powerful web applications and APIs.


### Compiles and hot-reloads for development
```
$ composer start
```

## License

The Multive Framework is licensed under the MIT license. See [License File](LICENSE.md) for more information.


## :sunglasses: THAT'S IT!

Now go build a cool Project.
