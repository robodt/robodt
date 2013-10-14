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
require 'filemanager.php';

use Robodt\Hooks;
use Robodt\Actions;
use Robodt\Settings;
use Robodt\FileManager;

use DirectoryIterator;
use dflydev\markdown\MarkdownParser as MarkdownParser;

class Robodt
{

	public $hooks;
	public $actions;
	protected $settings;
	protected $filemanager;
	protected $api;


	public function __construct() {
		$this->hooks = new Hooks;
		$this->actions = new Actions;
		$this->settings = new Settings;
		$this->filemanager = new FileManager;
		$this->api = array();

		// DEBUG: hardcoded markdown parser, change it!
		// $markdown = new MarkdownParser();

		// Register hooks and actions
		$this->actions->register('site.set', 'setSite', $this);
		$this->actions->register('request.render', 'requestRender', $this);

		// DEBUG: debug hooks, to be removed remove later on
		$this->hooks->register('debug', 'debugSettings', $this, 10);
		$this->hooks->register('debug', 'debugApi', $this, 100);

		// DEBUG: hard coded settings, change it!
		$this->settings->set('dir.sites', 'sites');
		$this->settings->set('dir.content', 'content');
		$this->settings->set('dir.themes', 'themes');
	}


	// TODO: implement
	public function render($uri, $site = false) {
		$this->hooks->execute('init');
		$this->actions->execute('site.set', array($site));
		$this->actions->execute('request.render', array($uri));
		$this->hooks->execute('debug');
	}


	public function requestRender($uri) {
		print "<h3>Content Tree</h3>\n<pre>\n";
		$content = array('.', 'sites', 'default', 'contents');
		$content = implode(DIRECTORY_SEPARATOR, $content);
		$content = new DirectoryIterator($content);
		print_r($this->filemanager->generateTree($content));
		print "\n</pre><hr />";
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


	// DEBUG FUNCTIONS


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