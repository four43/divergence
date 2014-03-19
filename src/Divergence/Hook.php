<?php

/*
 * Divergence - A small full featured PHP router
 * @author Seth Miller <seth@four43.com>
 * @see https://github.com/four43/divergence for full documentation
 */

namespace Divergence;

/**
 * Hook/Callback
 * 
 * A simple implementation of Hook/Callback event system.
 */
class Hook {
	
	/**
	 * Event fired before anything happens.
	 */
	const EVENT_PRE_REQUEST		 = 'pre_request';
	
	/**
	 * Event fired before most of the route is found.
	 */
	const EVENT_PRE_ROUTE_MATCH	 = 'pre_route_match';
	
	/**
	 * Event fired after the route is found, controller is instanciated. 
	 * *Note:* Do dependency injection here!
	 */
	const EVENT_POST_ROUTE_MATCH	 = 'post_route_match';
	
	/**
	 * Fired before the handler is called.
	 */
	const EVENT_PRE_DISPATCH = 'pre_handler';
	
	/**
	 * Fired after the handler, now has results.
	 */
	const EVENT_POST_DISPATCH = 'post_handler';
	
	/**
	 * Event fired after everything happens.
	 */
	const EVENT_POST_REQUEST		 = 'post_request';
	
	/**
	 * Fired when the route wasn't found.
	 */
	const EVENT_RESPONSE_NOT_FOUND = 'response_404';
	
	/**
	 * Fired when the method was not found for a given route.
	 */
	const EVENT_RESPONSE_NOT_ALLOWED = 'response_405';
	

	/**
	 * Instance
	 * 
	 * Singleton patter, stores an instance of itself.
	 * @var self
	 */
	protected static $instance;

	/**
	 * Hooks
	 * 
	 * Set of functions to call when an event is called.
	 * @var array
	 */
	protected $hooks;

	/**
	 * Add
	 * 
	 * Add a function to be called when a hook is fired
	 * 
	 * @param string $eventName
	 * @param callable $callback
	 */
	public static function subscribe(string $eventName, \callable $callback) {
		$instance						 = self::getInstance();
		$instance->hooks[$eventName][]	 = $callback;
	}

	/**
	 * Fire
	 * 
	 * Fire off an event with the specified params.
	 * 
	 * @param string $eventName
	 * @param type $params
	 */
	public static function fire(string $eventName, $params = null) {
		$instance = self::getInstance();
		if (isset($instance->hooks[$eventName])) {
			foreach ($instance->hooks[$eventName] as $function) {
				call_user_func_array($function, array(&$params));
			}
		}
	}

	public static function getInstance() {
		if (empty(self::$instance)) {
			self::$instance = new Hook();
		}
		return self::$instance;
	}

}
