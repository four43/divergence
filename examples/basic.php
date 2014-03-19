<?php
/**
 * Basic Example
 */
$routes = array(
	/**
	 * Matches any variable at the end of /endpoint/, and pass it to the controller
	 * and passes it to the controller 
	 */
	'/endpoint/:number' => new RouteConfig('RestV1/Controller/Endpoint'),
	
	/**
	 * You may specify any sort of callable to the config, a request like POST
	 * /news/breaking_story will lead to the function newDispatcher('breaking_story')
	 * being called. 
	 */
	'/news/:alpha' => new RouteConfig('newDispatcher'),
	
	/**
	 * Multiple variable may be passed at once, they will be matched and passed
	 * to the handler in the same order. GET /weather/minnesota/minneapolis will call
	 * the handler object's method get('minnesota', 'minneapolis')
	 */
	'/weather/:alpha/:alpha' => new RouteConfig('MyApp/Controller/MyController')
);