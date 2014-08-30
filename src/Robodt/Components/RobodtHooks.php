<?php

/**
 * Robodt - Markdown CMS
 * @author      Zomnium
 * @link        http://www.zomnium.com
 * @copyright   2014 Zomnium, Tim van Bergenhenegouwen
 */

namespace Robodt\Components;

interface RobodtHooks
{
    /* hooks */

    public function set($name, $callback);

    public function remove($name);

    public function apply($name);
}
