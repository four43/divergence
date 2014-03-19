<?php

/*
 * Divergence - A small full featured PHP router
 * @author Seth Miller <seth@four43.com>
 * @see https://github.com/four43/divergence for full documentation
 */
namespace Divergence;

/**
 * All of the methods that are needed for a full CRUD bases API.
 */
interface ControllerInterface {
	
	public function get();
	
	public function post();
	
	public function put();
	
	public function delete();
}
