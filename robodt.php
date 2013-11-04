<?php

/**
 * Robodt - Markdown CMS
 * @author      Zomnium
 * @link        http://www.zomnium.com
 * @copyright   2013 Zomnium, Tim van Bergenhenegouwen
 */

namespace Robodt;

require __dir__.DIRECTORY_SEPARATOR.'debug.php';
require __dir__.DIRECTORY_SEPARATOR.'hooks.php';
require __dir__.DIRECTORY_SEPARATOR.'actions.php';
require __dir__.DIRECTORY_SEPARATOR.'settings.php';
require __dir__.DIRECTORY_SEPARATOR.'filemanager.php';
require __dir__.DIRECTORY_SEPARATOR.'meta.php';
require __dir__.DIRECTORY_SEPARATOR.'content.php';
require __dir__.DIRECTORY_SEPARATOR.'navigation.debug.php'; // NOTE: implement the real deal

use Robodt\Debug;
use Robodt\Hooks;
use Robodt\Actions;
use Robodt\Settings;
use Robodt\FileManager;
use Robodt\Meta;
use Robodt\Content;
use Robodt\DebugNavigation; // TODO: implement the real deal

class Robodt
{
    public $debug;
    public $hooks;
    public $actions;
    protected $settings;
    protected $filemanager;
    protected $content;
    protected $api;
    protected $navigation;

    public function __construct()
    {
        $this->debug = new Debug;
        $this->hooks = new Hooks;
        $this->actions = new Actions;
        $this->settings = new Settings;
        $this->filemanager = new FileManager;
        $this->content = new Content;
        $this->api = array();
        $this->registerHooks();
        $this->navigation = new DebugNavigation; // TODO: implement the real deal
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
        $this->hooks->register('init', 'loadSettings', $this, 100);
        $this->hooks->register('request.prerender', 'loadApi', $this, 100);
        $this->hooks->register('request.render', 'requestRender', $this, 100);
        $this->hooks->register('request.postrender', 'debugApi', $this, 100);
        $this->debug->log( array('Robodt Location' => __dir__.DIRECTORY_SEPARATOR) );
    }

    /**
     * Load main settings
     */
    public function loadSettings()
    {
        $this->settings->load('settings.php');
    }

    /**
     * Collect API data
     */
    public function loadApi()
    {
        // Render settings
        $this->api['settings'] = $this->settings->get_all();

        // Get needed settings
        $root = $this->settings->get('dir.root');
        $site = $this->settings->get('dir.site');
        $content = $this->settings->get('dir.content');

        // Fill API values
        $this->api['site']['directory'] = $this->generatePath( array( $root, $site ) );
        $this->api['site']['content'] = $this->generatePath( array( $root, $site, $content ) );
        $this->api['filetree'] = $this->filemanager->getTree( $this->api['site']['content'] );

        // Generate url and file mappings
        $this->filemanager->generateIndex( $this->api['filetree'] );
        $this->api['index'] = $this->filemanager->getIndex();
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

        if (isset( $this->api['index'][ DIRECTORY_SEPARATOR . $this->generatePath( $uri ) ] ) ) {
            $file[] = $this->api['index'][ DIRECTORY_SEPARATOR . $this->generatePath( $uri ) ];
        }

        $this->api['request'] = $this->content->parseFile($file);
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

    /**
     * Debug API
     */
    public function debugApi()
    {
        $this->debug->log( array( 'API' => $this->api ) );
        $this->api['debug'] = $this->debug->logArray();
    }

}
