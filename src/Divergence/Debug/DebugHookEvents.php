<?php

/*
 * Divergence - A small full featured PHP router
 * @author Seth Miller <seth@four43.com>
 * @see https://github.com/four43/divergence for full documentation
 */

namespace Divergence\Debug;

use Divergence\Hook;

/**
 * DebugHookEvents
 * 
 * Add debug hook events to router, this is handy for dumping events and their
 * respective contents to the output during development.
 *
 * @author seth
 */
class DebugHookEvents {

	public static function registerCallbacks() {
		Hook::subscribe(Hook::EVENT_PRE_REQUEST, 'Divergence\Debug\DebugHookEvents::eventPreRequest');
		Hook::subscribe(Hook::EVENT_PRE_ROUTE_MATCH, 'Divergence\Debug\DebugHookEvents::eventPreRouteMatch');
		Hook::subscribe(Hook::EVENT_POST_ROUTE_MATCH, 'Divergence\Debug\DebugHookEvents::eventPostRouteMatch');
		Hook::subscribe(Hook::EVENT_PRE_DISPATCH, 'Divergence\Debug\DebugHookEvents::eventPreDispatch');
		Hook::subscribe(Hook::EVENT_POST_DISPATCH, 'Divergence\Debug\DebugHookEvents::eventPostDispatch');
		Hook::subscribe(Hook::EVENT_POST_REQUEST, 'Divergence\Debug\DebugHookEvents::eventPostRequest');
		Hook::subscribe(Hook::EVENT_RESPONSE_NOT_FOUND, 'Divergence\Debug\DebugHookEvents::eventResponseNotFound');
		Hook::subscribe(Hook::EVENT_RESPONSE_NOT_ALLOWED, 'Divergence\Debug\DebugHookEvents::eventResponseNotAllowed');
	}

	public static function eventPreRequest($event) {
		self::debugEvent(Hook::EVENT_PRE_REQUEST, $event);
	}

	public static function eventPreRouteMatch($event) {
		self::debugEvent(Hook::EVENT_PRE_ROUTE_MATCH, $event);
	}

	public static function eventPostRouteMatch($event) {
		self::debugEvent(Hook::EVENT_POST_ROUTE_MATCH, $event);
	}

	public static function eventPreDispatch($event) {
		self::debugEvent(Hook::EVENT_PRE_DISPATCH, $event);
	}

	public static function eventPostDispatch($event) {
		self::debugEvent(Hook::EVENT_POST_DISPATCH, $event);
	}

	public static function eventPostRequest($event) {
		self::debugEvent(Hook::EVENT_POST_REQUEST, $event);
	}

	public static function eventResponseNotFound($event) {
		self::debugEvent(Hook::EVENT_RESPONSE_NOT_FOUND, $event);
	}
	
	public static function eventResponseNotAllowed($event) {
		self::debugEvent(Hook::EVENT_RESPONSE_NOT_ALLOWED, $event);
	}
	
	public static function debugEvent($eventName, $event) {
		echo "Route Event: ".$eventName."\n";
		var_dump($event);
	}

}
