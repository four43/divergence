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

	protected $path;
	protected $callable;
	protected $groups;
	
	/**
	 * Construct
	 * 
	 * @param callable $callable
	 * @param string|array $groups
	 */
	public function __construct(string $callable, $groups = null) {
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
