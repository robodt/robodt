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

	public function run_hook($key, $parameters = '') {
		if ( ! isset($this->hooks[$key])) {
			return false;
		}

		$hooks = $this->hooks;
		$hooks = $this->hooks[$key];
		ksort($hooks);

		foreach ($hooks as $hook) {
			$this->execute_hook($hook['function'], $hook['class'], $parameters);
		}
	}

	private function execute_hook($function, $class, $parameters) {
		if ( ! $class && function_exists($function)) {
			print 'return function';
			return $function;
		}

		if ( ! class_exists($class)) {
			print 'class does not excist';
			return false;
		}

		$hook = new $class();
		return $hook->$function($parameters);
	}

	public function registered() {
		return $this->hooks;
	}

}