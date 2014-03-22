<?php

/**
 * Basic Example
 */
$routes = array(
	/**
	 * Matches any variable at the end of /endpoint/, and passes it to 
	 * the controller, will call the controller with the HTTP method 
	 */
	'/endpoint/:number' => 'RestV1/Controller/Endpoint',
	/**
	 * You may specify any sort of callable to the config, a request like POST
	 * /news/breaking_story will lead to the function newDispatcher('breaking_story')
	 * being called with 'breaking_story' as an argument.
	 */
	'/news/:alpha' => 'newDispatcher',
	/**
	 * Multiple variable may be passed at once, they will be matched and passed
	 * to the handler in the same order. GET /weather/minnesota/minneapolis will call
	 * the handler object's method get('minnesota', 'minneapolis')
	 */
	'/weather/:alnum/:alpha' => 'MyApp/Controller/MyController',
	/**
	 * You may also pass other data in the route config, just be sure to set a 
	 * 'handler' key. This extra meta data is available to callbacks. This can be
	 * useful for laying out your app in different modules, config for DI, etc.
	 */
	'/shows/:alnum' => array(
		'handler'	 => 'MyApp/Controller/MyController',
		'module'	 => 'RestV1'
	)
);
