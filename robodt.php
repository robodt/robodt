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

// use DirectoryIterator;
use dflydev\markdown\MarkdownParser as MarkdownParser;

class Robodt
{

	protected $hooks;
	protected $actions;
	protected $settings;
	protected $api;

	public function __construct() {
		$this->hooks = new Hooks;
		$this->actions = new Actions;
		$this->settings = new Settings;
		$this->api = array();

		// DEBUG: hardcoded markdown parser, change it!
		$markdown = new MarkdownParser();

		// Register hooks and actions
		$this->actions->register('site.set', 'setSite', $this);
		$this->actions->register('request.render', 'transformMarkdown', $markdown);

		// DEBUG: debug hooks, to be removed remove later on
		$this->hooks->register('debug', 'debugSettings', $this, 5);
		$this->hooks->register('debug', 'debugApi', $this, 1);

		// DEBUG: hard coded settings, change it!
		$this->settings->set('dir.sites', 'sites');
		$this->settings->set('dir.content', 'content');
		$this->settings->set('dir.themes', 'themes');
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

	public function render($uri, $site = false) {
		$this->hooks->execute('init');
		$this->actions->execute('site.set', array($site));
		print $this->actions->execute('request.render', array($uri));
		$this->hooks->execute('debug');
	}

	public function setSite($site = false) {
		if ( ! $site) {
			$site = $_SERVER['SERVER_NAME'];
		}

		if ( ! file_exists($site)) {
			$site = 'default';
		}

		$this->api['site'] = $site;

		return $site;
	}

	private function splitFile($contents)
	{
		$contents = str_replace("\r", "", $contents);
		return preg_split('![\r\n]+[-]{4,}!i', $contents);
	}

	private function parseMetadata($contents)
	{
		// Generate array from string
		$metadata = array();
		preg_match_all('/^(.+?):(.+)$/m', $contents, $metadata);

		// Convert array to key value format
		$metadata = array_combine($metadata[1], $metadata[2]);

		// Trim values and return
		return array_map('trim', $metadata);
	}

	public function debugApi() {
		print "<h3>API</h3>\n";
		print "<pre>\n";
		print_r($this->api);
		print "\n</pre><hr />";
	}

	public function debugSettings() {
		print "<h3>Settings</h3>\n";
		print "<pre>\n";
		print_r($this->settings->get_all());
		print "\n</pre><hr />";
	}

}