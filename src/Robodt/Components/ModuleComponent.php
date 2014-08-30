<?php

/**
 * Robodt - Markdown CMS
 * @author      Zomnium
 * @link        http://www.zomnium.com
 * @copyright   2014 Zomnium, Tim van Bergenhenegouwen
 */

namespace Robodt\Components;

abstract class ModuleComponent extends Component
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
