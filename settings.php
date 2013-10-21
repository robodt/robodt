<?php

/**
 * Robodt - Markdown CMS
 * @author      Zomnium
 * @link        http://www.zomnium.com
 * @copyright   2013 Zomnium, Tim van Bergenhenegouwen
 */

namespace Robodt;

class Settings extends Robodt
{
    protected $settings;

    public function __construct()
    {
        $this->settings = array();
    }

    public function load($file, $key = false)
    {
        if ( ! file_exists($file)) {
            return false;
        }

        $settings = file_get_contents($file, FILE_USE_INCLUDE_PATH);
        $settings = str_replace(array('<?php', '<?', 'die();', 'exit();'), '', $settings);
        $settings = json_decode($settings, true);

        if ($key) {
            $this->settings[$key] = $settings;
        } else {
            $this->settings = array_merge($this->settings, $settings);
        }

        return $settings;
    }

    public function set($key, $value)
    {
        $this->remove($key);
        $this->settings[$key] = $value;
    }

    public function get($key)
    {
        if ( ! isset($this->settings[$key])) {
            return false;
        }
        return $this->settings[$key];
    }

    public function remove($key)
    {
        if (isset($this->settings[$key])) {
            unset($this->settings[$key]);
        }
    }

    public function get_all()
    {
        return $this->settings;
    }

}