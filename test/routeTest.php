<?php

require_once 'autoloader.php';
// Instantiate the loader
$loader = new Psr4AutoloaderClass();
// register the autoloader
$loader->register();

// register the base directories for the namespace prefix
$loader->addNamespace('Divergence', __DIR__.'/../src/Divergence');
$loader->addNamespace('DivergenceTest', __DIR__.'/DivergenceTest');

class RouteTest extends PHPUnit_Framework_TestCase {
	
	const NOT_ALLOWED_RESPONSE = 'not allowed';
	const NOT_FOUND_RESPONSE = 'not found';
	
	public function testRouteCallableBasic() {
		$this->setRequest('GET', '/test/callable');
		$routes				 = array(
			'/test/callable' => function() {
				return 'callable-okay';
			},
			'/test/callable2'	 => 'nope'
		);
		$result = Divergence\Router::serve($routes);
		$this->assertEquals('callable-okay', $result);
	}
	
	public function testRouteCallableBasicFail() {
		$this->setRequest('POST', '/test/callable');
		$routes				 = array(
			'/test/callable' => 'DivergenceTest\TestController1'
		);
		$notAllowedResponse = null;
		Divergence\Hook::subscribe(Divergence\Hook::EVENT_RESPONSE_NOT_ALLOWED, function() use (&$notAllowedResponse) {
			$notAllowedResponse = RouteTest::NOT_ALLOWED_RESPONSE;
		});
		$result = Divergence\Router::serve($routes);
		$this->assertEquals(self::NOT_ALLOWED_RESPONSE, $notAllowedResponse);
	}
	
	public function testRouteCallableBasicHandlerFail() {
		$this->setRequest('POST', '/test/callable');
		$routes				 = array(
			'/test/callable' => 'DivergenceTest\TestController9001'
		);
		$notFoundResponse = null;
		Divergence\Hook::subscribe(Divergence\Hook::EVENT_RESPONSE_NOT_FOUND, function() use (&$notFoundResponse) {
			$notFoundResponse = RouteTest::NOT_FOUND_RESPONSE;
		});
		$result = Divergence\Router::serve($routes);
		$this->assertEquals(self::NOT_FOUND_RESPONSE, $notFoundResponse);
	}

	public function testRouteCallableBasicVarTest1() {
		$this->setRequest('GET', '/test/callable/hello');
		$routes				 = array(
			'/test/callable/:alpha' => function($var1) {
					return $var1.' world';
				},
			'/test/callable2'	 => 'nope'
		);
		$result = Divergence\Router::serve($routes);
		$this->assertEquals('hello world', $result);
	}

	public function testRouteCallableBasic2VarTest() {
		$this->setRequest('GET', '/test/callable/hello/world');
		$routes				 = array(
			'/test/callable/:alpha/:alpha' => function($var1, $var2) {
					return $var1.' '.$var2;
				},
			'/test/callable2'	 => 'nope'
		);
		$result = Divergence\Router::serve($routes);
		$this->assertEquals('hello world', $result);
	}

	public function testRouteCallableBasic2VarTestNull() {
		$this->setRequest('GET', '/test/callable/hello/');
		$routes				 = array(
			'/test/callable/:alpha/:alpha' => function($var1, $var2) {
					return $var1.' '.$var2;
				},
			'/test/callable2'	 => 'nope'
		);
		$notFoundResponse = null;
		Divergence\Hook::subscribe(Divergence\Hook::EVENT_RESPONSE_NOT_FOUND, function() use (&$notFoundResponse) {
			$notFoundResponse = RouteTest::NOT_FOUND_RESPONSE;
		});
		$result = Divergence\Router::serve($routes);
		$this->assertEquals(self::NOT_FOUND_RESPONSE, $notFoundResponse);
	}

	public function testRouteControllerBasic() {
		$this->setRequest('GET', '/test/place');
		$routes = array(
			'/test/place'	 => 'DivergenceTest\TestController1',
			'/test/pl'		 => function() {
					return 'no';
				}
		);
		$result = Divergence\Router::serve($routes);
		$this->assertEquals(DivergenceTest\TestController1::GET_SUCCESS, $result);
	}

	public function testRouteCustomTokens() {
		$this->setRequest('GET', '/test/place/helloworld');
		$routes = array(
			'/test/place/:custom'	 => 'DivergenceTest\TestController1',
			'/test/pl'		 => function() {
					return 'no';
				}
		);
		$tokens = array(
			':custom' => '(hello.*)'
		);
		$result = Divergence\Router::serve($routes, $tokens);
		$this->assertEquals(DivergenceTest\TestController1::GET_SUCCESS, $result);
		
		$this->setRequest('GET', '/test/place/badworld');
		
		$notFoundResponse = null;
		Divergence\Hook::subscribe(Divergence\Hook::EVENT_RESPONSE_NOT_FOUND, function() use (&$notFoundResponse) {
			$notFoundResponse = RouteTest::NOT_FOUND_RESPONSE;
		});
		$resultBad = Divergence\Router::serve($routes, $tokens);
		$this->assertEquals(self::NOT_FOUND_RESPONSE, $notFoundResponse);
	}
	
	public function testRouteArrayHandlerData() {
		$this->setRequest('GET', '/test/callable/hello');
		$routes				 = array(
			'/test/callable/:alpha' => array(
				'handler' => function($var1) {
						return $var1.' world';
					},
				'meta' => 'custom-data'
			),
			'/test/callable2'	 => 'nope'
		);
		$result = Divergence\Router::serve($routes);
		$this->assertEquals('hello world', $result);
	}
	
	public function testRouteCustomData() {
		$this->setRequest('GET', '/test/callable/hello');
		$routes				 = array(
			'/test/callable/:alpha' => array(
				'handler' => function($var1) {
						return $var1.' world';
					},
				'meta' => 'custom-data'
			),
			'/test/callable2'	 => 'nope'
		);
		$event = null;
		Divergence\Hook::subscribe(Divergence\Hook::EVENT_POST_ROUTE_MATCH, function($passedEvent) use (&$event) {
			$event = $passedEvent;
		});
		$result = Divergence\Router::serve($routes);
		$this->assertEquals('hello world', $result);
		$this->assertEquals('custom-data', $event['routeMatch']['meta']);
	}

	protected function setRequest($method, $path, array $data = array()) {
		$_SERVER['REQUEST_METHOD']	 = strtoupper($method);
		$_SERVER['REQUEST_URI']		 = $path;
	}

}
