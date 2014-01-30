<?php

/**
 * Robodt - Markdown CMS
 * @author      Zomnium
 * @link        http://www.zomnium.com
 * @copyright   2014 Zomnium, Tim van Bergenhenegouwen
 */

namespace Robodt;

class Components
{
    protected $chewbacca = true;
    private static $instance;
    private $components;
    public $framework;
    public $robodt;

    public function __construct(array $settings = array())
    {
        // Create singleton instance
        self::$instance = $this;

        // Register core components
        $this->framework = ( isset($settings['framework']) ? new $settings['framework'] : new \Slim\Slim );
        $this->setHooks( isset($settings['hooks']) ? new $settings['hooks'] : new \Robodt\SlimHooks );
        $this->setLogger( isset($settings['logger']) ? new $settings['logger'] : new \Robodt\SlimLogger );
        $this->setRouter( isset($settings['router']) ? new $settings['router'] : new \Robodt\SlimRouter );
        $this->setViews( isset($settings['views']) ? new $settings['views'] : new \Robodt\SlimViews );

        // Set additional components
        if (isset($settings['components']))
            $this->setComponents($settings['components']);

        // Register Robodt
        $this->robodt = new \Robodt\Robodt();
    }

    /**
     * Get Singleton instance from this class
     *
     * @return object Instance
     */
    public static function getInstance()
    {
        return self::$instance;
    }

    /**
     * Set component(s)
     *
     * @param string or array $companent Component name or key value array
     * @param object $resource Component class or null when $component is an array
     * @return object Component or null
     */
    public function set($component, $resource = null)
    {
        if (is_array($component) && $resource == null) {
            foreach ($component as $namespace => $resource) {
                $this->set($namespace, $resource);
            }
        } else {
            return $this->resources[$component] = new $resource;
        }
    }

    /**
     * Get component from register
     *
     * @param string $namespace Name for register lookup
     * @return object Component class
     */
    public function get($namespace)
    {
        return (isset( $this->resources[$namespace] )) ? $this->resources[$namespace] : false;
    }

    /**
     * Set a Robodt component
     *
     * @param string $component 
     * @param object $resource
     * @return null
     */
    public function setRobodtComponent($component, $resource)
    {
        // $this->robodt->setComponent($component, $resource);
    }

    /**
     * Hooks component
     */
    public function setHooks(RobodtHooks $hooks)
    {
        $this->set('hooks', $hooks);
        $this->setRobodtComponent('hooks', $hooks);
    }

    /**
     * Logger component
     */
    public function setLogger(RobodtLogger $logger)
    {
        $this->set('log', $logger);
        $this->setRobodtComponent('logger', $logger);
    }

    /**
     * Router component
     */
    public function setRouter(RobodtRouter $router)
    {
        $this->set('router', $router);
    }

    /**
     * Views component
     */
    public function setViews(RobodtViews $views)
    {
        $this->set('views', $views);
    }

    /**
     * Run application
     */
    public function run()
    {
        // Create framework interface
        print "Lets go!<br />\n";
        $this->framework->run();
    }
}
