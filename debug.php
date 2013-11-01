<?php

/**
 * Robodt - Markdown CMS
 * @author      Zomnium
 * @link        http://www.zomnium.com
 * @copyright   2013 Zomnium, Tim van Bergenhenegouwen
 */

namespace Robodt;

class Debug
{
    protected $log;

    public function __construct()
    {
        $this->log = array();
    }

    public function log($value)
    {
        $this->log[] = $value;
    }

    public function logArray()
    {
        return $this->log;
    }

    public function logHtml()
    {
        $output = "<hr />\n";
        foreach ($this->log as $value) {
            $output .= "<pre>" . print_r($value, true) . "</pre>\n";
        }
        return $output;
    }

}
