<?php

/**
 * Robodt - Markdown CMS
 * @author      Zomnium
 * @link        http://www.zomnium.com
 * @copyright   2013 Zomnium, Tim van Bergenhenegouwen
 */

namespace Robodt;

use Robodt\Component;

class SlimRouter extends Component implements RobodtRouter
{
    /* routing */

    public function get($route, $callback)
    {
        return $this->components->framework->get($route, $callback);
    }

    public function post($route, $callback)
    {
        return $this->components->framework->post($route, $callback);
    }

    public function put($route, $callback)
    {
        return $this->components->framework->put($route, $callback);
    }

    public function delete($route, $callback)
    {
        return $this->components->framework->delete($route, $callback);
    }
}

class SlimLogger extends Component implements RobodtLogger
{
    /* logging */

    public function add($record)
    {
        return true;
    }
}

class SlimHooks extends Component implements RobodtHooks
{
    /* hooks */

    public function set($name, $callback)
    {
        $this->components->framework->hook($name, $callback);
    }

    public function remove($name)
    {
        return false; // Not supported
    }

    public function apply($name)
    {
        $this->components->framework->applyHook($name);
    }
}

class SlimViews extends Component implements RobodtViews
{
    /* theming */

    public function get() {}

    public function set() {}
}
