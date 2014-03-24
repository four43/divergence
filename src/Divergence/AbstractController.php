<?php

/*
 * Divergence - A small full featured PHP router
 * @author Seth Miller <seth@four43.com>
 * @see https://github.com/four43/divergence for full documentation
 */
namespace Divergence;

/**
 * An example for a controller, feel free to extend it. It uses the 4 common 
 * CRUD methods.
 *
 * @author seth
 */
abstract class AbstractController implements ControllerInterface {
	
	/**
	 * Get - Read
	 * 
	 * Usually called with GET /v1/action/:number, the id can be blank which would
	 * get a list of objects, or if the id is passed return that object.
	 * 
	 * @param int $id
	 */
	public function get($id = null) {
		/*
		 * Return not found by defaut, just in case you don't override it when 
		 * extending it.
		 */
		Hook::fire(Hook::EVENT_RESPONSE_NOT_FOUND);
	}
	
	/**
	 * Post - Create
	 * 
	 * Posted data is used to create objects. This data may be encoded in a number
	 * of ways, commonly json data. 
	 */
	public function post() {
		$data = self::getPostData();
		/*
		 * Return not found by defaut, just in case you don't override it when 
		 * extending it.
		 */
		Hook::fire(Hook::EVENT_RESPONSE_NOT_FOUND);
	}
	
	/**
	 * Put - Update
	 * 
	 * Put is used to update data, the object id is passed then updated with the 
	 * passed data (in the "POST body").
	 * @param int $id
	 */
	public function put($id) {
		$data = self::getPostData();
		/*
		 * Return not found by defaut, just in case you don't override it when 
		 * extending it.
		 */
		Hook::fire(Hook::EVENT_RESPONSE_NOT_FOUND);
	}
	
	/**
	 * Delete
	 * 
	 * Removing an object is done with the delete method. Pass an id with the 
	 * request and the object should be removed.
	 * @param int $id
	 */
	public function delete($id) {
		/*
		 * Return not found by defaut, just in case you don't override it when 
		 * extending it.
		 */
		Hook::fire(Hook::EVENT_RESPONSE_NOT_FOUND);
	}
	
	protected static function getPostData() {
		switch($_SERVER['CONTENT_TYPE']) {
			case 'application/json':
				return json_decode(file_get_contents('php://input'), true);
			case 'application/x-www-form-urlencoded':
				return $_POST;
			default:
				return array();
		}
	}
	
}
