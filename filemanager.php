<?php

/**
 * Robodt - Markdown CMS
 * @author      Zomnium
 * @link        http://www.zomnium.com
 * @copyright   2013 Zomnium, Tim van Bergenhenegouwen
 */

namespace Robodt;

use Robodt\Filters;
use Robodt\Content;
use DirectoryIterator;

class FileManager {

    protected $content;

    public function __construct()
    {
        $this->content = new Content;
    }



    /****** New implementation ******/


    // Filetree
    // Index
    // Navigation


    public function indexContent($dir, $uri = array())
    {
        //
    }

    private function createDirectoryObject($dir)
    {
        return new DirectoryIterator( ( is_array($dir) ? Filters::arrayToPath($dir) : $dir ) );
    }

/*

- Check if file or dir
- If dir, recursion
- If index:
    - Render metadata
    - Create index record (uri/path)
    - Create Navigation item

*/

    /****** Old implementation ******/


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

    public function generateIndex($tree, $uri = '', $path = '')
    {
        $index = array();

        foreach ($tree as $key => $value) {
            if (is_array($value)) {
                $index = array_merge($index,
                    $this->generateIndex(
                        $value,
                        $uri . DIRECTORY_SEPARATOR . Filters::pathToUri($key),
                        $path . DIRECTORY_SEPARATOR . $key )
                    );
            }
            if ($value == 'index.txt') {
                $index[$uri] = $path . DIRECTORY_SEPARATOR . 'index.txt';
            }
        }

        return $index;
    }

    public function generateNavigation($request, $tree, $current = '', $level = -1, $active = false, $uri = array(), $path = array())
    {
        $navigation = array();
        $items = array();

        // Loop through tree
        foreach ($tree as $key => $value) {

            // Generate current values
            $tmp_uri = $uri;
            $tmp_uri[] = Filters::pathToUri($key);
            $tmp_path = $path;
            $active = ( ( $uri[$level] == $request[$level] && ( $level == 0 || $active ) ) ? true : false );

            // Recursion
            if (is_array($value)) {
                $tmp_path[] = $key;
                $items[] = $this->generateNavigation(
                    $request,
                    $value,
                    $key,
                    $level + 1,
                    $active,
                    $tmp_uri,
                    $tmp_path
                );
            }

            // Index found, add page
            if ($value == 'index.txt') {
                $tmp_path[] = 'index.txt';
                $navigation['title'] = $current;
                $navigation['url'] = '/' . implode('/', $uri);
                $navigation['active'] = $active;
                $navigation['debug'] = $this->content->parseFile($tmp_path);
            }
        }

        // Add items value
        $navigation['items'] = (count($items) > 0 ? $items : false );

        // Remove items value for highest records
        $navigation = ($level < 0 ? $navigation['items'] : $navigation);

        // Return records
        return $navigation;
    }

}
