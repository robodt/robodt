<?php

/**
 * Robodt - Markdown CMS
 * @author      Zomnium
 * @link        http://www.zomnium.com
 * @copyright   2013 Zomnium, Tim van Bergenhenegouwen
 */

namespace Robodt;

use DirectoryIterator;

class FileManager {

    protected $index;
    protected $navigation;

    public function __construct()
    {
        $this->index = array();
        $this->navigation = array();
    }

	/**
	 * Transform filepath to DirectoryIterator
	 *
	 * @param array or string $dir Path to transform
	 * @return array Directory tree
	 */
    public function getTree($dir)
    {
        if (is_array($dir)) {
            $dir = implode(DIRECTORY_SEPARATOR, $dir);
        }
        $dir = new DirectoryIterator($dir);
        return $this->generateTree($dir);
    }

    private function generateTree( DirectoryIterator $dir )
    {
        $data = array();
        foreach ( $dir as $node ) {
            if ( $node->isDir() && !$node->isDot() ) {
                $data[$node->getFilename()] = $this->generateTree( new DirectoryIterator( $node->getPathname() ) );
            } else if ( $node->isFile() ) {
                if ( substr( $node->getFilename(), 0, 1) != '.') {
                    $data[] = $node->getFilename();
                }
            }
        }
        return $data;
    }

    public function getIndex()
    {
        return $this->index;
    }

    public function generateIndex($tree, $uri = '', $path = '')
    {
        foreach ($tree as $key => $value) {
            if (is_array($value)) {
                $this->generateIndex(
                    $value,
                    $uri . DIRECTORY_SEPARATOR . $this->generateUrl($key),
                    $path . DIRECTORY_SEPARATOR . $key );
            }
            if ($value == 'index.txt') {
                $this->index[$uri] = $path . DIRECTORY_SEPARATOR . 'index.txt';
            }
        }
    }

    public function getNavigation()
    {
        return $this->navigation;
    }

    public function generateNavigation($tree, $uri = '')
    {
        foreach ($tree as $key => $value) {
            
        }
    }

    public function generateUrl($input)
    {
        $input = explode('.', $input);

        if (is_array($input) && count($input) < 2) {
            $input = $input[0];
        }

        if (is_array($input) && count($input) > 1) {
            array_shift( $input );

            if (is_array($input)) {
                $input = implode('.', $input);
            }
        }
        return $input;
    }

}
