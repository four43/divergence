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
		Divergence\Hook::subscribe(Divergence\Hook::EVENT_RESPONSE_NOT_FOUND, function() {
			global $notFoundResponse;
			$notFoundResponse = 'not found';
		});
		$result = Divergence\Router::serve($routes);
		$this->assertEquals('not found', $notFoundResponse);
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

	public function setRequest($method, $path, array $data = array()) {
		$_SERVER['REQUEST_METHOD']	 = strtoupper($method);
		$_SERVER['REQUEST_URI']		 = $path;
	}

}
