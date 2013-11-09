<?php

/**
 * Robodt - Markdown CMS
 * @author      Zomnium
 * @link        http://www.zomnium.com
 * @copyright   2013 Zomnium, Tim van Bergenhenegouwen
 */

namespace Robodt;

require __dir__.DIRECTORY_SEPARATOR.'filters.php';
require __dir__.DIRECTORY_SEPARATOR.'hooks.php';
require __dir__.DIRECTORY_SEPARATOR.'actions.php';
require __dir__.DIRECTORY_SEPARATOR.'settings.php';
require __dir__.DIRECTORY_SEPARATOR.'crawler.php';
require __dir__.DIRECTORY_SEPARATOR.'content.php';

use Robodt\Filters;
use Robodt\Hooks;
use Robodt\Actions;
use Robodt\Settings;
use Robodt\Crawler;
use Robodt\Content;

class Robodt
{
    public $hooks;
    public $actions;
    protected $settings;
    protected $crawler;
    protected $content;
    protected $api;
    protected $navigation;
    protected $site;

    public function __construct($site)
    {
        $this->site = $site;
        $this->hooks = new Hooks;
        $this->actions = new Actions;
        $this->settings = new Settings;
        $this->crawler = new Crawler;
        $this->content = new Content;
        $this->api = array();
        $this->registerHooks();
    }

    /**
     * Register main hooks
     */
    private function registerHooks()
    {
        $this->hooks->register('init', 'loadSettings', $this, 100);
        $this->hooks->register('request.prerender', 'loadApi', $this, 100);
        $this->hooks->register('request.render', 'requestRender', $this, 100);
    }

    /**
     * Load main settings
     */
    public function loadSettings()
    {
        $this->settings->load(
            Filters::arrayToPath(
                array(
                    $this->site,
                    'settings',
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

        $this->api['settings']['site.directory'] = Filters::arrayToUri(array(
            $this->settings->get('dir.root'),
            $this->settings->get('dir.site')
            ));

        $this->api['settings']['site.content'] = Filters::arrayToUri(array(
            $this->settings->get('dir.root'),
            $this->settings->get('dir.site'),
            $this->settings->get('dir.content')
            ));
    }

    /**
     * Parse requested page from file
     * 
     * @param array $uri Uri from request
     */
    public function requestRender($uri)
    {
        $content = Filters::arrayToPath( array( $this->site, 'content' ) );
        $this->api = array_merge($this->api, $this->crawler->indexContent( $content, $uri ) );

        $file = array();
        $file[] = $content;

        if (isset( $this->api['index'][ Filters::arrayToUri( $uri ) ] ) ) {
            $file[] = $this->api['index'][ Filters::arrayToUri( $uri ) ];
        } else {
            $file[] = '404';
        }

        $this->api['request'] = $this->content->parseFile($file);
        $this->api['request']['uri'] = $uri;
        $this->api['request']['file'] = $file;
    }

    /**
     * Execute all important hooks to render request
     * 
     * @param array $uri HTTP request uri
     * @param string $site Hostname - optional
     * @return array API data
     */
    public function render($uri)
    {
        // NOTE: filter uri
        $this->hooks->execute('init');
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

}
