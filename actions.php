<?php

/**
 * Robodt - Markdown CMS
 * @author      Zomnium
 * @link        http://www.zomnium.com
 * @copyright   2013 Zomnium, Tim van Bergenhenegouwen
 */

namespace Robodt;

class Actions extends Robodt
{

	protected $actions;

	public function __construct() {
		$this->actions = array();
	}

	public function register($key, $function, $class) {
		$this->remove($key);
		$this->actions[$key] = array(
				'function'		=> $function,
				'class'			=> $class
			);
	}

	public function remove($key) {
		if (isset($this->actions[$key])) {
			unset($this->actions[$key]);
		}
	}

	public function execute($key, $parameters = array()) {
		if ( ! isset($this->actions[$key])) {
			return false;
		}
		return call_user_func_array(array($this->actions[$key]['class'], $this->actions[$key]['function']), $parameters);
	}

	public function registered() {
		return $this->actions;
	}

}