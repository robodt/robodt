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
    protected $dir;

    public function __construct()
    {
        $this->content = new Content;
    }


    /****** New implementation ******/

/*

Generate:
- Filetree
- Index
- Navigation

Approach:
- List directory
    - If dir:
        - Add to tree
        - Recursion for children files/folders
    - If index:
        - Add to tree
        - Render metadata
        - Create index record:
            uri
            path
        - Create Navigation item:
            title
            uri
            active
            subitems

*/

    public function indexContent($dir, $uri = array(), $home = array())
    {
        $data = array();
        $dir = $this->createDirectoryObject($dir);
        $data['filetree'] = $this->createTree($dir);
        $data['index'] = $this->createIndex($data['tree']);
        $data['navigation'] = $this->createNavigation($data['tree']);
        return $data;
    }

    private function createDirectoryObject($dir)
    {
        return new DirectoryIterator( ( is_array($dir) ? Filters::arrayToPath($dir) : $dir ) );
    }

    private function createTree(DirectoryIterator $dir)
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


    /****** Old implementation ******/


	/**
	 * Transform filepath to DirectoryIterator
	 *
	 * @param array or string $dir Path to transform
	 * @return array Directory tree
	 */
    public function getTree($dir)
    {
        $this->dir = Filters::arrayToPath($dir);
        return $this->generateTree(
            $this->createDirectoryObject($dir) );
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
                $tmp_file = $this->dir . Filters::arrayToPath($path) . DIRECTORY_SEPARATOR . 'index.txt';
                $tmp_file = $this->content->parseFile( $tmp_file );
                $uri = ( isset($tmp_file['metadata']['url']) ? $tmp_file['metadata']['url'] : $uri );
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
            $tmp_file = null;
            $tmp_active = ( ( $uri[$level] == $request[$level] && ( $level == 0 || $active ) ) ? true : false );

            // Recursion
            if (is_array($value)) {
                $tmp_path[] = $key;
                $items[] = $this->generateNavigation(
                    $request,
                    $value,
                    $key,
                    $level + 1,
                    $tmp_active,
                    $tmp_uri,
                    $tmp_path
                );
            }

            // Index found, add page
            if ($value == 'index.txt') {
                array_unshift($tmp_path, $this->dir);
                array_push($tmp_path, 'index.txt');
                $tmp_path = Filters::arrayToPath( $tmp_path );
                $tmp_file = $this->content->parseFile( $tmp_path );
                $navigation['title'] = $tmp_file['metadata']['title'];
                $navigation['title'] = ( isset($tmp_file['metadata']['navigation']) ? $tmp_file['metadata']['navigation'] : $navigation['title'] );
                $navigation['url'] = ( isset($tmp_file['metadata']['url']) ? $tmp_file['metadata']['url'] : '/' . implode('/', $uri) );
                if (isset($tmp_file['metadata']['url'])) {
                    $navigation['active'] = ( ( Filters::uriRemovePrefix( $tmp_file['metadata']['url'] ) == $request[$level] && ( $level == 0 || $active ) ) ? true : $tmp_active );
                } else {
                    $navigation['active'] = $tmp_active;
                }

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
