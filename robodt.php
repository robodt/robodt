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
	var $settings;
	var $api;

	function __construct() {
		$this->hooks = new Hooks;
		$this->actions = new Actions;
		$this->settings = new Settings;
		$this->api = array();
	}

	/*
		Create API:
		- Settings?
		- Site?
		- Tree / Navigation
		- Status code: 200 / 404 / 500
		- Content
		- Metadata
		- Settings?
	*/

	public function render($uri, $site) {
		$this->hooks->execute('init');
		$this->hooks->execute('site');
	}

	public function set_site($site = false) {
		if ( ! $site) {
			$site = $_SERVER['SERVER_NAME'];
		}

		if ( ! file_exists($site)) {
			$site = 'default';
		}

		$this->api['site'] = $site;

		return $site;
	}

}