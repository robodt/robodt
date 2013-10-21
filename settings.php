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
    protected $settings = array();

    /**
     * Load settings from file
     *
     * @param string $file Path to file
     * @param string $key Register key - optional
     * @return array Settings from file
     */
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

    /**
     * Set setting(s) in register
     *
     * @param string $key Register key
     * @param array or string $value Setting value(s)
     */
    public function set($key, $value)
    {
        $this->remove($key);
        $this->settings[$key] = $value;
    }

    /**
     * Get setting(s) from register
     *
     * @param string $key Register key
     * @return array or string Register value
     */
    public function get($key)
    {
        if ( ! isset($this->settings[$key])) {
            return false;
        }
        return $this->settings[$key];
    }

    /**
     * Remove setting(s) from register
     *
     * @param string $key Register key
     */
    public function remove($key)
    {
        if (isset($this->settings[$key])) {
            unset($this->settings[$key]);
        }
    }

    /**
     * Get all settings from register
     *
     * @return array Register settings
     */
    public function get_all()
    {
        return $this->settings;
    }

}