<?php

/**
 * Robodt - Markdown CMS
 * @author      Zomnium
 * @link        http://www.zomnium.com
 * @copyright   2014 Zomnium, Tim van Bergenhenegouwen
 */

namespace Robodt;

abstract class Component
{
    protected $components;

    public function __construct()
    {
        $this->components = Components::getInstance();
    }
}

abstract class Module extends Component
{
    public function __construct()
    {
        parent::__construct();
    }

    public function berry()
    {
        return 'Berry!';
    }
}

abstract class ContentComponent extends Component
{
    public function __construct()
    {
        parent::__construct();
    }
}
