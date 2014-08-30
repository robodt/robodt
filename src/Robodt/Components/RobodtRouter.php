<?php

/**
 * Robodt - Markdown CMS
 * @author      Zomnium
 * @link        http://www.zomnium.com
 * @copyright   2014 Zomnium, Tim van Bergenhenegouwen
 */

namespace Robodt\Components;

interface RobodtRouter
{
    /* routing */

    public function get($route, $callback);

    public function post($route, $callback);

    public function put($route, $callback);

    public function delete($route, $callback);

    // Abstractions:
    // - parameters
    // - wildcard
    // - optional parameters
    // - conditions
    // - route names (generating urls)
    // - generate url helper(s)
    // - middleware?
}
