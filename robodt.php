<?php

/**
 * Robodt - Markdown CMS
 * @author      Zomnium
 * @link        http://www.zomnium.com
 * @copyright   2013 Zomnium, Tim van Bergenhenegouwen
 */

namespace Robodt;

require 'hooks.php';
require 'actions.php';
require 'settings.php';

use Robodt\Hooks;
use Robodt\Actions;
use Robodt\Settings;
use DirectoryIterator;
use dflydev\markdown\MarkdownParser as MarkdownParser;

class Robodt
{

	var $hooks;
	var $actions;

	function __construct() {
		$this->hooks = new Hooks;
		$this->actions = new Actions;

		$this->actions->register_action('woop', 'woopwoob', __CLASS__, 0);
		$this->hooks->register_hook('bootstrap', 'bootstrap_bye', __CLASS__, 1, 10);
		$this->hooks->register_hook('bootstrap', 'bootstrap_message', __CLASS__, 1, 1);
		$this->hooks->run_hook('bootstrap', array('world'));
		$this->actions->run_action('woop', array('Onniee', 'TJAKKAAAA!'));
	}

	public function bootstrap_message($message = 'robodt') {
		print 'Hello, ' . $message . '!<br />';
	}

	public function bootstrap_bye($message = 'robodt') {
		print 'Goobye, ' . $message . '.<br />';
	}

	public function woopwoob($first, $second) {
		print 'WOOOOOOOP! ' . $first . ', ' . $second . '<br />';
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