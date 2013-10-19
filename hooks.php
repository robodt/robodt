<?php

/**
 * Robodt - Markdown CMS
 * @author      Zomnium
 * @link        http://www.zomnium.com
 * @copyright   2013 Zomnium, Tim van Bergenhenegouwen
 */

namespace Robodt;

class Hooks
{

	public $hooks;

	public function __construct() {
		$this->hooks = array();
	}

	public function register($key, $function, $class, $priority = 10) {
		$this->hooks[$key][$priority] = array(
				'function'		=> $function,
				'class'			=> $class
			);
	}

	public function overwrite($key, $function, $class, $priority = 10) {
		$this->remove($key);
		$this->register_hook($key, $function, $class, $priority);
	}

	public function remove($key) {
		if (isset($this->hooks[$key])) {
			unset($this->hooks[$key]);
		}
	}

	public function execute($key, $parameters = array()) {
		if ( ! isset($this->hooks[$key])) {
			return false;
		}

		$hooks = $this->hooks[$key];
		krsort($hooks);

		foreach ($hooks as $hook) {
			call_user_func_array(array($hook['class'], $hook['function']), $parameters);
		}
	}

	public function registered() {
		return $this->hooks;
	}

}