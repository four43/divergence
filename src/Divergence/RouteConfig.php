<?php

/*
 * Divergence - A small full featured PHP router
 * @author Seth Miller <seth@four43.com>
 * @see https://github.com/four43/divergence for full documentation
 */

/**
 * RouteConfig
 *
 * Some route configuration, set the "path" as the key to your array passed to
 * Router::serve
 */
class RouteConfig {
	
	public $callable;
	public $groups;
	public $meta;
	
	/**
	 * Construct
	 * 
	 * @param callable $callable - A valid PHP callable, classes will be instanciated
	 * @param string|array $groups - A group to keep this route.
	 * @param mixed $meta - Can be anything you would like to pass along with
	 *		the route.
	 */
	public function __construct(string $callable, $groups = null, $meta = null) {
		//$this->path		 = $path;
		$this->callable	 = $callable;
		if (is_array($groups)) {
			$this->groups = $groups;
		}
		else if (is_string($groups) && $groups != '') {
			$this->groups = array($groups);
		}
	}

}
