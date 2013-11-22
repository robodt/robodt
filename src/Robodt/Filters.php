<?php

/**
 * Robodt - Markdown CMS
 * @author      Zomnium
 * @link        http://www.zomnium.com
 * @copyright   2013 Zomnium, Tim van Bergenhenegouwen
 */

namespace Robodt;

class Filters
{


    /**
     * Validators
     * 
     * @return boolean
     */

    static function validateUrl($url)
    {
        return filter_var($url, FILTER_VALIDATE_URL);
    }

    static function validateUri($uri)
    {
        return $this->validateUrl($uri);
    }

    static function validateHost($host)
    {
        return $this->validateUrl($host);
    }

    static function validateIp($ip)
    {
        return filter_var($ip, FILTER_VALIDATE_IP);
    }

    static function validateEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }



    /**
     * Sanitizers
     */

    /**
     * Sanitize URI
     *
     * @param array or string $uri
     * @return array Filtered URI
     */
    static function sanitizeUri($uri)
    {
        $uri = self::arrayToUri($uri);
        $uri = str_replace(array(' ', './', '../', '/.'), '-', $uri);
        $uri = filter_var($uri, FILTER_SANITIZE_URL);
        $uri = filter_var($uri, FILTER_SANITIZE_ENCODED);
        return self::uriToArray($uri);
    }



    /**
     * Convertors
     */

    /**
     * From path to uri
     *
     * @param string $path
     * @return string URI generated from path name
     */
    static function pathToUri($path)
    {
        $path = explode('.', $path);
        if (is_array($path) && count($path) < 2) {
            $path = $path[0];
        }
        if (is_array($path) && count($path) > 1) {
            array_shift( $path );
            if (is_array($path)) {
                $path = implode('.', $path);
            }
        }
        return $path;
    }

    static function arrayToPath($path)
    {
        return ( is_array($path) ? implode(DIRECTORY_SEPARATOR, $path) : $path );
    }

    /**
     * Generate, filter and stringify file path's
     *
     * @param string or array $path File path to filter and transform
     * @return string Filtered and stringified file path
     */
    static function arrayToUri($uri)
    {
        return ( is_array($uri) ? implode('/', $uri) : $uri );
    }

    /**
     * From URI to array
     *
     * @param string $uri
     * @return array Same URI as input
     */
    static function uriToArray($uri)
    {
        return explode('/', $uri);
    }

    /**
     * From path to array
     *
     * @param string $path
     * @return array Same path as input
     */
    static function pathToArray($path)
    {
        return explode(DIRECTORY_SEPARATOR, $path);
    }

    /**
     * Remove URI prefix slash
     *
     * @param string or array $uri
     * @return string Same uri as input
     */
    static function uriRemovePrefix($uri)
    {
        $uri = self::arrayToUri($uri);
        return ( substr($uri, 0, 1) == '/' ? substr($uri, 1) : $uri );
    }

}
