Divergence Router
=================

A fast router for PHP, as easy, or as full featured as you need it.

Insipred by ToroRouter (https://github.com/anandkunal/ToroPHP), which is a great
router to quickly create simple apps. Divergence aims to be almost as simple, but
provide more features for larger apps.

Build Status
------------
Master: [![Build Status](https://travis-ci.org/four43/divergence.svg?branch=master)](https://travis-ci.org/four43/divergence)

Development: [![Build Status](https://travis-ci.org/four43/divergence.svg?branch=development)](https://travis-ci.org/four43/divergence)

Features
--------

* *Simple* - Single file rouder, commented and easy to understand.
* *Debug* - Provided debug handler, add it to your app temporarily to see what callbacks
get called when, and with what data.
* *Server Setup* - Use provided server configs (.htaccess for Apache and web.config for IIS) to route
all of your requests to your index.php file.

Example
-------

###Basic
```php
<?php
$routes = array(
	'/v1/action/:number' => 'RestV1\Controller\Action'
);
\Divegent\Router::serve($routes);
```
Will route `/v1/action/123` to the controller `RestV1\Controller\Action` based 
on the method, `GET` will call the `get()` method as `get(123)`

###Basic - Callback



