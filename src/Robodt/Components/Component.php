<?php

/**
 * Robodt - Markdown CMS
 * @author      Zomnium
 * @link        http://www.zomnium.com
 * @copyright   2014 Zomnium, Tim van Bergenhenegouwen
 */

namespace Robodt\Components;

use \Robodt\Robodt as Robodt;

abstract class Component
{
    protected $robodt;

    public function __construct()
    {
        $this->robodt = Robodt::getInstance();
    }
}
