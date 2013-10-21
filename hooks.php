<?php

/**
 * Robodt - Markdown CMS
 * @author      Zomnium
 * @link        http://www.zomnium.com
 * @copyright   2013 Zomnium, Tim van Bergenhenegouwen
 */

namespace Robodt;

class Hooks
{
    private $hooks = array();

    /**
     * Register hook in register
     *
     * @param string $key Register key
     * @param string $function Function name
     * @param string $class Class name
     * @param int $priority Priority of executing (higher is more importent) - optional
     */
    public function register($key, $function, $class, $priority = 10)
    {
        $this->hooks[$key][$priority] = array(
                'function'        => $function,
                'class'           => $class
            );
    }

    /**
     * Overwrite registed hook with a new one
     */
    public function overwrite($key, $function, $class, $priority = 10)
    {
        $this->remove($key);
        $this->register_hook($key, $function, $class, $priority);
    }

    /**
     * Remove hook from register
     *
     * @param string $key Register key
     */
    public function remove($key)
    {
        if (isset($this->hooks[$key])) {
            unset($this->hooks[$key]);
        }
    }

    /**
     * Execute hooked functions
     *
     * @param string $key Register key
     * @param array $parameters Parameters for executed functions - optional
     * @return returns false on empty register key
     */
    public function execute($key, $parameters = array())
    {
        if ( ! isset($this->hooks[$key])) {
            return false;
        }

        $hooks = $this->hooks[$key];
        krsort($hooks);

        foreach ($hooks as $hook) {
            call_user_func_array(array($hook['class'], $hook['function']), $parameters);
        }
    }

    /**
     * Get register
     *
     * @return array Register
     */
    public function registered()
    {
        return $this->hooks;
    }

}