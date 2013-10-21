<?php

/**
 * Robodt - Markdown CMS
 * @author      Zomnium
 * @link        http://www.zomnium.com
 * @copyright   2013 Zomnium, Tim van Bergenhenegouwen
 */

namespace Robodt;

class Actions
{
    protected $actions = array();

    /**
     * Register action in register
     *
     * @param string $key Register key
     * @param string $function Function name
     * @param string $class Class name
     */
    public function register($key, $function, $class)
    {
        $this->remove($key);
        $this->actions[$key] = array(
                'function'        => $function,
                'class'           => $class
            );
    }

    /**
     * Remove action from register
     *
     * @param string $key Register key
     */
    public function remove($key)
    {
        if (isset($this->actions[$key])) {
            unset($this->actions[$key]);
        }
    }

    /**
     * Execute action function
     *
     * @param string $key Register key
     * @param array $parameters Parameters for executed functions - optional
     * @return returns function result
     */
    public function execute($key, $parameters = array())
    {
        if ( ! isset($this->actions[$key])) {
            return false;
        }
        return call_user_func_array(array($this->actions[$key]['class'], $this->actions[$key]['function']), $parameters);
    }

    /**
     * Get register
     *
     * @return array Register
     */
    public function registered()
    {
        return $this->actions;
    }

}