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
	 * 		'/endpoint/:id' => new RouteConfig('MyApp\Controller\Endpoint', 'RestV1'),
	 * 		'/movies/:genre/:movieId' => new RouteConfig('MyApp\Controller\Movies')
	 * 	));
	 * @param Route[] $routes
	 */
	public static function serve(array $routes) {
		Hook::fire(Hook::EVENT_PRE_REQUEST, compact('routes'));

		$requestMethod = strtolower($_SERVER['REQUEST_METHOD']);

		//Find Path Info
		$pathInfo = self::getPathInfo();
		
		Hook::fire(Hook::EVENT_PRE_ROUTE_MATCH, compact('routes', 'requestMethod', 'pathInfo'));
		
		//Route Match
		$routeMatch = null;
		if (isset($routes[$pathInfo])) {
			$routeMatch = $routes[$pathInfo];
		}
		else if ($routes) {
			$tokens = array(
				':alpha'	 => '([a-zA-Z0-9-_]+)',
				':alnum'	 => '([0-9a-zA-Z-_]+)',
				':number'	 => '([0-9]+)',
				':string'	 => '([a-zA-Z-_]+)',
			);
			
			foreach ($routes as $pattern => $handlerName) {
				$pattern = strtr($pattern, $tokens);
				$matches = array();
				if (preg_match('#^/?'.$pattern.'/?$#', $pathInfo, $matches)) {
					$matchedHandler	 = $handlerName;
					$regexMatches		 = $matches;
					break;
				}
			}
		}
		$result = null;
		$handlerInstance = null;
		if ($matchedHandler) {
			if (is_string($matchedHandler)) {
				$handlerInstance = new $matchedHandler();
			}
			elseif (is_callable($matchedHandler)) {
				$handlerInstance = $matchedHandler();
			}
		}
		
		Hook::fire(Hook::EVENT_POST_ROUTE_MATCH, compact('routes', 'requestMethod', 'pathInfo', 'matchedHandler', 'handlerInstance', 'regexMatches'));

		if ($handlerInstance) {
			unset($regexMatches[0]);

			if (self::isXhrRequest() && method_exists($handlerInstance, $requestMethod.'Xhr')) {
				self::setXhrHeaders();
				$requestMethod .= 'Xhr';
			}

			if (method_exists($handlerInstance, $requestMethod)) {
				Hook::fire(Hook::EVENT_PRE_DISPATCH, compact('routes', 'requestMethod', 'pathInfo', 'matchedHandler', 'handlerInstance', 'regexMatches'));
				$result = call_user_func_array(array($handlerInstance, $requestMethod), $regexMatches);
				Hook::fire(Hook::EVENT_POST_DISPATCH, compact('routes', 'requestMethod', 'pathInfo', 'matchedHandler', 'handlerInstance', 'regexMatches', 'result'));
			}
			else {
				Hook::fire('404', compact('routes', 'requestMethod', 'pathInfo', 'matchedHandler', 'handlerInstance', 'regexMatches', 'result'));
			}
		}
		else {
			Hook::fire('404', compact('routes', 'requestMethod', 'pathInfo', 'matchedHandler', 'handlerInstance', 'regexMatches', 'result'));
		}
		
		Hook::fire(Hook::EVENT_POST_REQUEST, compact($routes));
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

	protected static function isXhrRequest() {
		return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
	}
	
	protected static function setXhrHeaders() {
		header('Content-type: application/json');
		header('Expires: Sun, 12 Mar 1989 00:00:00 GMT');
		header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
		header('Cache-Control: no-store, no-cache, must-revalidate');
		header('Cache-Control: post-check=0, pre-check=0', false);
		header('Pragma: no-cache');
	}

	protected static function responseMethodNotAllowed() {
		http_response_code(405);
	}

	protected static function responseNotFound() {
		http_response_code(404);
	}

}


