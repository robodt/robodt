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

}
