<?php

/*
 * Divergence - A small full featured PHP router
 * @author Seth Miller <seth@four43.com>
 * @see https://github.com/four43/divergence for full documentation
 */
namespace Divergence\Debug;

use Divergence\Hook;
/**
 * Description of DebugHookEvents
 *
 * @author seth
 */
class DebugHookEvents {
	
	public static function registerCallbacks() {
		
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
	
	public static function eventPreHandler($event) {
		self::debugEvent(Hook::EVENT_PRE_DISPATCH, $event);
	}
	
	public static function eventPostHandler($event) {
		self::debugEvent(Hook::EVENT_POST_DISPATCH, $event);
	}
	
	public static function eventPostRequest($event) {
		self::debugEvent(Hook::EVENT_POST_REQUEST, $event);
	}
	
	public static function debugEvent($eventName, $event) {
		echo "Route Event: ".$eventName."\n";
		var_dump($event);
	}
}
