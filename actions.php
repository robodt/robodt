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

	var $actions;

	function __construct() {
		$this->actions = array();
	}

	public function register_action($key, $function, $class, $parameters = 0) {
		$this->remove_action($key);
		$this->actions[$key] = array(
				'function'		=> $function,
				'class'			=> $class,
				'parameters'	=> $parameters
			);
	}

	public function remove_action($key) {
		if (isset($this->actions[$key])) {
			unset($this->actions[$key]);
		}
	}

	public function run_action($key, $parameters = array()) {
		if ( ! isset($this->actions[$key])) {
			return false;
		}
		call_user_func_array(array($this->actions[$key]['class'], $this->actions[$key]['function']), $parameters);
	}

	public function registered_actions() {
		return $this->actions;
	}

}