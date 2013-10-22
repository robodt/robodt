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
require __dir__.DIRECTORY_SEPARATOR.'navigation.debug.php';

use Robodt\Hooks;
use Robodt\Actions;
use Robodt\Settings;
use Robodt\FileManager;
use Robodt\Meta;
use Robodt\Content;
use Robodt\DebugNavigation;

class Robodt
{
    public $hooks;
    public $actions;
    protected $settings;
    protected $filemanager;
    protected $content;
    protected $api;
    protected $navigation;

    public function __construct()
    {
        $this->hooks = new Hooks;
        $this->actions = new Actions;
        $this->settings = new Settings;
        $this->filemanager = new FileManager;
        $this->content = new Content;
        $this->api = array();
        $this->registerHooks();
        $this->navigation = new DebugNavigation;
    }

    /**
     * Execute all important hooks to render request
     * 
     * @param array $uri HTTP request uri
     * @param string $site Hostname - optional
     * @return array API data
     */
    public function render($uri, $site = false)
    {
        $this->hooks->execute('init', array($site));
        $this->hooks->execute('request.prerender');
        $this->hooks->execute('request.render', array($uri));
        $this->hooks->execute('request.postrender');
        return $this->api();
    }

    /**
     * Get API data
     * 
     * @return array API data
     */
    public function api()
    {
        return $this->api;
    }

    /**
     * Register main hooks
     */
    private function registerHooks()
    {
        $this->hooks->register('init', 'loadSettings', $this, 300);
        $this->hooks->register('init', 'setSite', $this, 200);
        $this->hooks->register('init', 'loadSiteSettings', $this, 100);
        $this->hooks->register('request.prerender', 'loadApi', $this, 100);
        $this->hooks->register('request.render', 'requestRender', $this, 100);
        $this->hooks->register('request.postrender', 'debugApi', $this, 100);
        $this->debug('Robodt Location', __dir__.DIRECTORY_SEPARATOR);
    }

    /**
     * Load main settings
     */
    public function loadSettings()
    {
        $this->settings->load('settings.php');
    }

    /**
     * Set current site
     * 
     * @param string $site Hostname - optional
     */
    public function setSite($site = false)
    {
        if ( ! $site) {
            $site = $_SERVER['SERVER_NAME'];
        }
        if ( ! file_exists($this->sitePath($site))) {
            $site = 'default';
        }
        if ( ! file_exists($this->sitePath($site))) {
            die('Requested site and default fallback could not be found.');
        }
        $this->api['site']['name'] = $site;
    }

    /**
     * Load site settings
     */
    public function loadSiteSettings()
    {
        $this->settings->load(
            $this->generatePath(
                array(
                    $this->sitePath(),
                    'site',
                    'settings.php'
                    )
                )
            );
    }

    /**
     * Collect API data
     */
    public function loadApi()
    {
        $this->api['settings'] = $this->settings->get_all();
        $this->api['site']['directory'] = $this->sitePath();
        $this->api['site']['content'] = implode(DIRECTORY_SEPARATOR, array(
            $this->sitePath(),
            $this->settings->get('dir.content')
            ));
        $this->api['filetree'] = $this->filemanager->getTree($this->api['site']['content']);
        $this->api['navigation'] = $this->navigation->items();
    }

    /**
     * Parse requested page from file
     * 
     * @param array $uri Uri from request
     */
    public function requestRender($uri)
    {
        $file = array();
        $file[] = $this->api['site']['content'];
        if (count($uri) > 0) {
            $file = array_merge($file, $uri);
        }
        $file[] = "index.txt";
        $this->api['request'] = $this->content->parseFile($file);
    }

    /**
     * Generate absolute path to site directory
     *
     * @param string $site Hostname
     * @return string absolute site file path - optional
     */
    public function sitePath($site = false)
    {
        if ( ! $site) {
            $site = $this->api['site']['name'];
        }
        $site = array(
            $this->settings->get('dir.root'),
            $this->settings->get('dir.sites'),
            $site
            );
        return implode(DIRECTORY_SEPARATOR, $site);
    }

    /**
     * Generate, filter and stringify file path's
     *
     * @param string or array $path File path to filter and transform
     * @return string Filtered and stringified file path
     */
    public function generatePath($path)
    {
        if (is_array($path)) {
            $path = implode(DIRECTORY_SEPARATOR, $path);
        }
        return $path;
    }


    /*
     * DEBUG FUNCTIONS
     */

    public function debug($title, $value)
    {
        $this->api['debug'][$title] = $value;
    }

    public function debugOutputArray()
    {
        return $this->api['debug'];
    }

    public function debugOutputHtml()
    {
        $output = "<hr />\n";
        foreach ($this->api['debug'] as $title => $value) {
            $output .= "<h3>" . $title . "</h3>\n";
            $output .= "<pre>" . print_r($value, true) . "</pre>\n";
        }
        return $output;
    }

    public function debugApi()
    {
        $this->debug('API', $this->api);
    }

}
