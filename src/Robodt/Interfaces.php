<?php

/**
 * Robodt - Markdown CMS
 * @author      Zomnium
 * @link        http://www.zomnium.com
 * @copyright   2014 Zomnium, Tim van Bergenhenegouwen
 */

namespace Robodt;

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

interface RobodtLogger
{
    /* logging */

    public function add($record);
}

interface RobodtHooks
{
    /* hooks */

    public function set($name, $callback);

    public function remove($name);

    public function apply($name);
}

interface RobodtErrorhandling
{
    /* error handling */

    public function error();
}

interface RobodtViews
{
    /* theming */

    public function get();

    public function set();
}

interface RobodtData
{
    /* data and active records */

    public function get();

    public function find();

    public function set();

    public function delete();
}

    // Interface?
    // Framework implementation?

    // Routing / Router - interface
    // Logging / Logger - interface - with fallback?
    // Hooks - interface - with fallback

    // Theming / Template (handler)
    // Boostrapping / Bootstrap

    // Extendable (routes, admin, plugins, etc.)


    // Functions:
    // - __construct: site
    // - mainRoute: route
    // - get: route, callback
    // - run

interface RobodtExtended
{
    /* dependency injection */

    public function setNamespace($namespace, $resource);

    public function getNamespace();

    /* request */

    public function headersRequest();

    public function cookiesRequest();

    /* caching */

    public function httpCaching();

    /* sesssion */

    public function getSession();

    public function setSession();

    /* flash messages */

    public function flashMessage(); // now, next, keep

}
