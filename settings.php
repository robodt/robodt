<?php

/**
 * Robodt - Markdown CMS
 * @author      Zomnium
 * @link        http://www.zomnium.com
 * @copyright   2013 Zomnium, Tim van Bergenhenegouwen
 */

namespace Robodt;

class Settings
{

	function __construct() {
		//
	}

	public function loadSettings($settings)
	{
		if (file_exists($settings)) {
			$settings = file_get_contents($settings, FILE_USE_INCLUDE_PATH);
			$settings = str_replace(array('<?php', '<?', 'die();', 'exit();'), '', $settings);
			return json_decode($settings, true);
		}
		else {
			return false;
		}
	}

}