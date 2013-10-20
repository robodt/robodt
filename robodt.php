<?php

/**
 * Robodt - Markdown CMS
 * @author      Zomnium
 * @link        http://www.zomnium.com
 * @copyright   2013 Zomnium, Tim van Bergenhenegouwen
 */

namespace Robodt;

require __dir__.DIRECTORY_SEPARATOR.'hooks.php';
require __dir__.DIRECTORY_SEPARATOR.'actions.php';
require __dir__.DIRECTORY_SEPARATOR.'settings.php';
require __dir__.DIRECTORY_SEPARATOR.'filemanager.php';
require __dir__.DIRECTORY_SEPARATOR.'meta.php';
require __dir__.DIRECTORY_SEPARATOR.'content.php';

class Robodt
{

	public $hooks;
	public $actions;
	protected $settings;
	protected $filemanager;
	protected $content;
	protected $api;


	public function __construct() {
		$this->hooks = new Hooks;
		$this->actions = new Actions;
		$this->settings = new Settings;
		$this->filemanager = new FileManager;
		$this->content = new Content;
		$this->api = array();

		// Register hooks and actions
		$this->hooks->register('init', 'getSettings', $this, 10);
		$this->hooks->register('site.set', 'setSite', $this, 10);
		$this->hooks->register('site.set', 'loadSettings', $this, 20);
		$this->hooks->register('request.render', 'requestRender', $this, 10);
		$this->hooks->register('request.postrender', 'debugApi', $this, 100);
		$this->debug('Robodt Location', __dir__.DIRECTORY_SEPARATOR);
	}


	public function render($uri, $site = false) {
		$this->hooks->execute('init');
		$this->hooks->execute('site.set', array($site));
		$this->hooks->execute('request.prerender');
		$this->hooks->execute('request.render', array($uri));
		$this->hooks->execute('request.postrender');
		return $this->api;
	}


	public function requestRender($uri) {
		$content = array($this->sitePath(), 'contents');
		$this->api['filetree'] = $this->filemanager->getTree($content);
		if (count($uri) > 0) {
			$content = array_merge($content, $uri);
		}
		$content = implode(DIRECTORY_SEPARATOR, $content) . DIRECTORY_SEPARATOR . "index.txt";
		$this->api['request'] = $this->content->parseFile($content);
	}


	public function getSettings() {
		$this->settings->load('settings.php');
	}


	public function setSite($site = false) {
		if ( ! $site) {
			$site = $_SERVER['SERVER_NAME'];
		}
		if ( ! file_exists($this->sitePath($site))) {
			$site = 'default';
		}
		if ( ! file_exists($this->sitePath($site))) {
			die('Requested site and default fallback could not be found.');
		}
		$this->api['site'] = $site;
		return $site;
	}


	public function sitePath($site = false) {
		if ( ! $site) {
			$site = $this->api['site'];
		}
		$site = array(
			$this->settings->get('dir.root'),
			$this->settings->get('dir.sites'),
			$site
			);
		return implode(DIRECTORY_SEPARATOR, $site);
	}


	public function loadSettings() {
		$this->api['settings'] = $this->settings->get_all();
	}


	/*
	 * DEBUG FUNCTIONS
	 */


	public function debug($title,$value) {
		$this->api['debug'][$title] = $value;
	}


	public function debugOutputArray() {
		return $this->api['debug'];
	}


	public function debugOutputHtml() {
		$output = "<hr />\n";
		foreach ($this->api['debug'] as $title => $value) {
			$output .= "<h3>" . $title . "</h3>\n";
			$output .= "<pre>" . print_r($value, true) . "</pre>\n";
		}
		return $output;
	}


	public function debugApi() {
		$this->debug('API', $this->api);
	}


}