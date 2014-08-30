<?php

/**
 * Robodt - Markdown CMS
 * @author      Zomnium
 * @link        http://www.zomnium.com
 * @copyright   2014 Zomnium, Tim van Bergenhenegouwen
 */

namespace Robodt;

class Robodt
{
    private static $instance;
    private $components;
    public $framework;

    /**
     * Constructor
     *
     * @param array Core settings and components
     * @return null
     */
    public function __construct(array $settings = array())
    {
        // Create singleton instance
        self::$instance = $this;

        // Register core components
        $this->framework = ( isset($settings['framework']) ? new $settings['framework'] : new \Slim\Slim );
        $this->setHooks( isset($settings['hooks']) ? new $settings['hooks'] : new \Robodt\Components\SlimHooks );
        $this->setLogger( isset($settings['logger']) ? new $settings['logger'] : new \Robodt\Components\SlimLogger );
        $this->setRouter( isset($settings['router']) ? new $settings['router'] : new \Robodt\Components\SlimRouter );
        $this->setViews( isset($settings['views']) ? new $settings['views'] : new \Robodt\Components\SlimViews );

        // Create process
        $this->set('process', new \Robodt\Process);
        $this->get('process')->register('render', $this->framework->run(), $this);
        // init
        // prerender
        // render
        // postrender
        // exit

        // Overwrite optional components and set aditional
        if (isset($settings['components']))
            $this->setComponents($settings['components']);
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
        // Register multiple components
        if (is_array($component) && $resource == null)
        {
            foreach ($component as $namespace => $resource)
            {
                $this->set($namespace, $resource);
            }
        }

        // Register single component
        else
        {
            return $this->components[$component] = new $resource;
        }
    }

    /**
     * Isset component(s)
     */
    public function exists($components) {
        if (is_array($components)) {
            foreach ($components as $namespace) {
                $component[$namespace] = $this->isset($namespace);
            }
            return $components;
        }
        else
        {
            return isset($this->components[$components]);
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
        return ($this->exists($namespace)) ? $this->components[$namespace] : false;
    }

    /**
     * Hooks component
     */
    public function setHooks(Components\RobodtHooks $hooks)
    {
        $this->set('hooks', $hooks);
    }

    /**
     * Logger component
     */
    public function setLogger(Components\RobodtLogger $logger)
    {
        $this->set('log', $logger);
    }

    /**
     * Router component
     */
    public function setRouter(Components\RobodtRouter $router)
    {
        $this->set('router', $router);
    }

    /**
     * Views component
     */
    public function setViews(Components\RobodtViews $views)
    {
        $this->set('views', $views);
    }

    /**
     * Run application
     */
    public function run()
    {
        $this->framework->run();
    }
}
