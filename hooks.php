<?php

/**
 * Robodt - Markdown CMS
 * @author      Zomnium
 * @link        http://www.zomnium.com
 * @copyright   2013 Zomnium, Tim van Bergenhenegouwen
 */

namespace Robodt;

class Hooks extends Robodt
{

	var $hooks;

	function __construct() {
		$this->hooks = array();
	}

	public function register_hook($key, $function, $class, $parameters = 0, $priority = 10) {
		$this->hooks[$key][$priority] = array(
				'function'		=> $function,
				'class'			=> $class,
				'parameters'	=> $parameters,
			);
	}

	public function overwrite_hook($key, $function, $class, $parameters = 0, $priority = 10) {
		$this->remove_hook($key);
		$this->register_hook($key, $function, $class, $parameters, $priority);
	}

	public function remove_hook($key) {
		if (isset($this->hooks[$key])) {
			unset($this->hooks[$key]);
		}
	}

	public function run_hook($key, $parameters = array()) {
		if ( ! isset($this->hooks[$key])) {
			return false;
		}

		$hooks = $this->hooks[$key];
		ksort($hooks);

		foreach ($hooks as $hook) {
			call_user_func_array(array($hook['class'], $hook['function']), $parameters);
		}
	}

	public function registered_hooks() {
		return $this->hooks;
	}

}