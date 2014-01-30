<?php

/**
 * Robodt - Markdown CMS
 * @author      Zomnium
 * @link        http://www.zomnium.com
 * @copyright   2013 Zomnium, Tim van Bergenhenegouwen
 */

namespace Robodt;

use Robodt\Filters;
use Robodt\Settings;
use Robodt\Crawler;
use Robodt\Content;

class Robodt
{
    public $hooks;
    public $logs;

    protected $settings;
    protected $api;
    protected $navigation;
    protected $site;

    protected $crawler;
    protected $content;

    public function __construct(array $config = array())
    {
        // Initialize config and defaults
        $this->site = ( isset($config['site']) ? $config['site'] : __dir__ . '/../../../../../site/' );
        $this->hooks = ( isset($config['hooks']) ? $config['hooks'] : new \Robodt\Hooks );
        $this->settings = new Settings;

        // Yet to be implemented
        // $this->logs = ( isset($config['logs']) ? $config['logs'] : new \Robodt\Logs );

        // Change implementations
        $this->crawler = new Crawler;
        $this->content = new Content;

        // Init
        $this->api = array();
        $this->registerHooks();
    }

    /**
     * Register main hooks
     */
    private function registerHooks()
    {
        $this->hooks->register('robodt.init', 'loadSettings', $this, 100);
        $this->hooks->register('robodt.render', 'render', $this, 100);
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
     * Parse requested page from file
     * 
     * @param array $uri Uri from request
     */
    public function render($uri)
    {
        $file = array();
        $file[] = Filters::arrayToPath( array( $this->site, 'content' ) );
        $request = Filters::arrayToUri( $uri );

        $this->api = array_merge($this->api, $this->crawler->indexContent( $file[0], $uri ) );

        if (isset( $this->api['index'][ $request ] ) ) {
            $file[] = $this->api['index'][ $request ];
        } else {
            $file[] = '404';
            $file[] = 'index.txt';
        }

        $this->api['settings'] = $this->settings->get_all();
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
    public function get($uri)
    {
        $uri = Filters::sanitizeUri($uri);
        $this->hooks->execute('robodt.init');
        $this->hooks->execute('robodt.prerender');
        $this->hooks->execute('robodt.render', array($uri));
        $this->hooks->execute('robodt.postrender');
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
