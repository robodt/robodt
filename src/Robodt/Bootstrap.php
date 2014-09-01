<?php

/**
 * Robodt - Markdown CMS
 * @author      Zomnium
 * @link        http://www.zomnium.com
 * @copyright   2014 Zomnium, Tim van Bergenhenegouwen
 */

namespace Robodt;

use Robodt\Components\Component as Component;

interface Bootstrap
{
    public function register();

    public function settings();
}
