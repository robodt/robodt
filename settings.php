<?php

/**
 * Robodt - Markdown CMS
 * @author      Zomnium
 * @link        http://www.zomnium.com
 * @copyright   2013 Zomnium, Tim van Bergenhenegouwen
 */

namespace Robodt;

class Settings extends Robodt
{

	var $settings;

	function __construct() {
		$this->settings = array();
	}

	public function load($file, $key = false) {
		if ( ! file_exists($file)) {
			return false;
		}

		$settings = file_get_contents($settings, FILE_USE_INCLUDE_PATH);
		$settings = str_replace(array('<?php', '<?', 'die();', 'exit();'), '', $settings);

		if ($key) {
			$this->settings[$key];
		}

		return json_decode($settings, true);
	}

	public function get($key) {
		if ( ! isset($this->settings[$key])) {
			return false;
		}
		return $this->settings[$key];
	}

	public function get_all() {
		return $this->settings;
	}

}