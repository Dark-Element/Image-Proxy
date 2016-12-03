<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 03/12/16
 * Time: 09:03
 */

class Auth {
	private $authenticated;

	public function __construct($params) {
		$this->authenticated = false;
		$this->authenticate($params);
	}

	public function is_authenticated(){
		return $this->authenticated;
	}

	private function authenticate($params){
		$start  = microtime();
		$this->times['auth'] = microtime() - $start;
		$this->authenticated = true;
	}
}