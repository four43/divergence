<?php

/*
 * Divergence - A small full featured PHP router
 * @author Seth Miller <seth@four43.com>
 * @see https://github.com/four43/divergence for full documentation
 */

namespace Divergence;

/**
 * Router
 * 
 * The main class of Divergent, the actual router.
 * 
 * Todo:
 * 
 * * Add support for a cache.
 * * Process and cache route groups.
 * 
 */
class Router {

	/**
	 * Serve
	 * 
	 * The main entry method, pass an array of routes.
	 * Pass routes config in this format:
	 * 
	 * Example:
	 * 	Route::serve(array(
	 * 		'/endpoint/:id' => 'MyApp\Controller\Endpoint',
	 * 		'/movies/:genre/:movieId' => array(
	 * 			'handler' => 'MyApp\Controller\Movies',
	 * 			'group' => 'RestV1',
	 * 			'meta' => 'custom'
	 * 		)
	 * 	));
	 * @param array $routes
	 * @param array $customTokens
	 */
	public static function serve(array $routes, array $customTokens = array()) {
		//Add custom regex shortcuts
		$tokens = array(
			':alpha'	 => '([a-zA-Z]+)',
			':alnum'	 => '([a-zA-Z0-9\-_]+)',
			':number'	 => '([0-9]+)',
			':string'	 => '([a-zA-Z\-_]+)',
		);
		if (!empty($customTokens)) {
			$tokens = array_merge($tokens, $customTokens);
		}

		Hook::fire(Hook::EVENT_PRE_REQUEST, compact('routes', 'tokens'));

		$requestMethod = strtolower($_SERVER['REQUEST_METHOD']);

		//Find Path Info
		$pathInfo = self::getPathInfo();

		Hook::fire(Hook::EVENT_PRE_ROUTE_MATCH, compact('routes', 'requestMethod', 'pathInfo'));

		//Route Match - Find which route this request is for.
		$routeMatch		 = null;
		$matchedHandler	 = null;
		$regexMatches	 = array();
		if (isset($routes[$pathInfo])) {
			//Literal match to route
			$routeMatch		 = $routes[$pathInfo];
			$matchedHandler	 = $routes[$pathInfo];
		}
		else if ($routes) {
			//Regex Match Route
			foreach ($routes as $pattern => $routeConfig) {
				$pattern = strtr($pattern, $tokens);
				$matches = array();
				if (preg_match('#^/?'.$pattern.'/?$#', $pathInfo, $matches)) {
					//Matched the path

					$routeMatch		 = $routeConfig;
					$regexMatches	 = $matches;
					if (is_string($routeConfig) || is_callable($routeConfig) || is_object($routeConfig)) {
						$matchedHandler = $routeConfig;
						break;
					}
					else if (is_array($routeConfig) && isset($routeConfig['handler'])) {
						$matchedHandler = $routeConfig['handler'];
						break;
					}
					else {
						throw new RouteConfigException("Wasn't able to find handler in route: "
						.$pattern." didn't pass a string, callable, or array config.");
					}
					//Keep looping if this is a bad config.
				}
			}
		}

		Hook::fire(Hook::EVENT_POST_ROUTE_MATCH, compact('routes', 'requestMethod', 'pathInfo', 'routeMatch', 'matchedHandler', 'handlerInstance', 'regexMatches'));

		$handlerInstance = null;
		$result			 = null;
		if (!empty($regexMatches)) {
			unset($regexMatches[0]);
		}
		if ($matchedHandler) {
			if (is_string($matchedHandler)) {
				//Matched Handler is a string, should be the name of and object, lets instantiate it.
				if (class_exists($matchedHandler)) {
					$handlerInstance = new $matchedHandler();

					//Dispatch handler
					if (method_exists($handlerInstance, $requestMethod)) {
						Hook::fire(Hook::EVENT_PRE_DISPATCH, compact('routes', 'requestMethod', 'pathInfo', 'routeMatch', 'matchedHandler', 'handlerInstance', 'regexMatches'));
						$result = call_user_func_array(array($handlerInstance, $requestMethod), $regexMatches);
						Hook::fire(Hook::EVENT_POST_DISPATCH, compact('routes', 'requestMethod', 'pathInfo', 'routeMatch', 'matchedHandler', 'handlerInstance', 'regexMatches', 'result'));
					}
					else {
						Hook::fire(Hook::EVENT_RESPONSE_NOT_ALLOWED, compact('routes', 'requestMethod', 'pathInfo', 'routeMatch', 'matchedHandler', 'handlerInstance', 'regexMatches'));
					}
				}
				else {
					Hook::fire(Hook::EVENT_RESPONSE_NOT_FOUND, compact('routes', 'requestMethod', 'pathInfo', 'routeMatch', 'matchedHandler', 'handlerInstance', 'regexMatches'));
				}
			}
			elseif (is_callable($matchedHandler)) {
				//If callable, simply call it with the data we have.
				//@todo Make callable template
				Hook::fire(Hook::EVENT_PRE_DISPATCH, compact('routes', 'requestMethod', 'pathInfo', 'routeMatch', 'matchedHandler', 'handlerInstance', 'regexMatches'));
				$result = call_user_func_array($matchedHandler, $regexMatches);
				Hook::fire(Hook::EVENT_POST_DISPATCH, compact('routes', 'requestMethod', 'pathInfo', 'routeMatch', 'matchedHandler', 'handlerInstance', 'regexMatches', 'result'));
			}
		}
		else {
			//Didn't find a handler
			Hook::fire(Hook::EVENT_RESPONSE_NOT_FOUND, compact('routes', 'requestMethod', 'pathInfo', 'regexMatches'));
		}

		Hook::fire(Hook::EVENT_POST_REQUEST, compact('routes', 'requestMethod', 'pathInfo', 'routeMatch', 'matchedHandler', 'handlerInstance', 'regexMatches', 'result'));

		return $result;
	}

	/**
	 * Get Path Info
	 * 
	 * Find path info from the request
	 * @return string
	 */
	protected static function getPathInfo() {
		$pathInfo = '/';
		if (!empty($_SERVER['PATH_INFO'])) {
			$pathInfo = $_SERVER['PATH_INFO'];
		}
		else if (!empty($_SERVER['ORIG_PATH_INFO']) && $_SERVER['ORIG_PATH_INFO'] !== '/index.php') {
			$pathInfo = $_SERVER['ORIG_PATH_INFO'];
		}
		else {
			if (!empty($_SERVER['REQUEST_URI'])) {
				$pathInfo = (strpos($_SERVER['REQUEST_URI'], '?') > 0) ? strstr($_SERVER['REQUEST_URI'], '?', true) : $_SERVER['REQUEST_URI'];
			}
		}
		return $pathInfo;
	}

}
