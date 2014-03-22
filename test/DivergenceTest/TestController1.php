<?php

/*
 * Divergence - A small full featured PHP router
 * @author Seth Miller <seth@four43.com>
 * @see https://github.com/four43/divergence for full documentation
 */

namespace DivergenceTest;

/**
 * Description of Controller1
 *
 * @author seth
 */
class TestController1 {

	const GET_SUCCESS = 'get-success';

	public function get() {
		return self::GET_SUCCESS;
	}

}
