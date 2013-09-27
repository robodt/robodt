<?php

/**
 * Robodt - Markdown CMS
 * @author      Zomnium
 * @link        http://www.zomnium.com
 * @copyright   2013 Zomnium, Tim van Bergenhenegouwen
 */

namespace Robodt;

require 'hooks.php';
require 'settings.php';

use Robodt\Hooks;
use Robodt\Settings;
use DirectoryIterator;
use dflydev\markdown\MarkdownParser as MarkdownParser;

class Robodt
{

	var $hooks;

	function __construct() {
		$this->hooks = new Hooks;
		$this->hooks->register_hook('bootstrap', 'bootstrap_bye', 'this', 1, 10);
		$this->hooks->register_hook('bootstrap', 'bootstrap_message', 'this', 1, 1);
		$this->hooks->run_hook('bootstrap', 'world');
	}

	public function bootstrap_message($message) {
		print 'Hello, ' . $message . '!';
	}

	public function bootstrap_bye($message) {
		print '<br />Goobye, ' . $message . '.';
	}

	public function API() {
		/*
			Create API:
			- Tree / Navigation
			- Status code: 200 / 404 / 500
			- Content
			- Metadata
			- Settings?
		*/
	}

}