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

class Crawler
{
    protected $content;
    protected $dir;
    protected $index;

    public function __construct()
    {
        $this->content = new Content;
    }

    /**
     * Index all content from directory
     *
     * @param array or string $dir Content directory to index
     * @param array $request Current request URI
     * @return array All data like index, file tree and navigation
     */
    public function indexContent($dir, $request = array())
    {
        $this->dir = Filters::arrayToPath($dir);
        $dir = $this->createDirectoryObject($dir);
        $data = $this->contentCrawler($dir, $request);
        $data['index'] = $this->index;
        $data['dev'] = $this->mapper($data['tree']);
        return $data;
    }

    private function mapper($tree)
    {
        return array_map(array($this, 'map'), $tree);
    }

    private function map($node)
    {
        // print $node . "<br />\n";
        if (is_array($node)) {
            print "next<br />\n";
            return $this->mapper($node);
        }
        return $node;
    }

    /**
     * Create Directory Object
     *
     * @param array or string $dir File path to create object from
     * @return DirectoryIterator Object
     */
    private function createDirectoryObject($dir)
    {
        return new DirectoryIterator( ( is_array($dir) ? Filters::arrayToPath($dir) : $dir ) );
    }

    /**
     * Content crawler
     *
     * @param DirectoryIterator $dir Directory path to crawl
     * @param array $request Current requested uri
     * @param array $uri Current uri from root/home
     * @param array $path Current path from root directory
     * @return array
     */
    private function contentCrawler( DirectoryIterator $dir, $request, $uri = array(), $path = array() )
    {
        $data = array();

        $current_uri = $uri;
        $current_metadata = $this->getMetadata($path);

        if ($current_metadata) {
            $current_uri[] = $this->createUri($path, $current_metadata);
            $this->createIndex($path, $current_uri);
            $current_navigation = $this->createNavigation( $request, $current_uri, $current_metadata );
        }

        foreach ($dir as $node) {

            if (substr($node->getFilename(), 0, 1) != '.') {

                if ($node->isDir() && !$node->isDot()) {

                    $new_path = $path;
                    $new_path[] = $node->getFilename();

                    $sub = $this->contentCrawler(
                        new DirectoryIterator( $node->getPathname() ),
                        $request,
                        $current_uri,
                        $new_path);

                    $data['tree'][$node->getFilename()] = $sub['tree'];

                    if ($sub['navigation']) {
                        $current_navigation['items'][] = $sub['navigation'];
                    }

                } else if ($node->isFile()) {
                    $data['tree'][] = $node->getFilename();
                }
            }

            // http://us2.php.net/manual/en/function.array-diff-assoc.php
            // $this->cache[ Filters::arrayToPath( array($node->getPathname(), $node->getFilename()) ) ] = $node->getCTime();
        }

        $current_navigation['items'] = ( ( isset($current_navigation['items']) && count($current_navigation['items']) > 0 ) ? $current_navigation['items'] : false );
        $current_navigation = ( ( isset($current_metadata['hidden']) && $current_metadata['hidden'] == true ) ? false : $current_navigation );
        $data['navigation'] = ( ( count($path) > 0 ) ? $current_navigation : $current_navigation['items'] );

        return $data;
    }

    /**
     * Get metadata
     *
     * @param array $path Path to file
     * @return array Metadata
     */
    private function getMetadata($path)
    {
        $file = array();
        $file[] = $this->dir;
        if ( count($path) > 0 ) $file = array_merge($file, $path);
        $file[] = 'index.txt';
        $file = $this->content->parseFile( $file );
        return ( isset($file['metadata']) ? $file['metadata'] : false );
    }

    /**
     * Create URI
     *
     * @param array $path Path to file
     * @param array $metadata File's metadata
     * @return string Uri
     */
    private function createUri($path, $metadata)
    {
        if ($metadata && isset($metadata['url'])) {
            return $metadata['url'];
        } else {
            return Filters::pathToUri(end($path));
        }
    }

    /**
     * Create index record
     *
     * @param array or string $path Path to index.txt file
     * @param array $uri Uri that matches index.txt file
     */
    private function createIndex($path, $uri)
    {
        $path[] = 'index.txt';
        $this->index[Filters::arrayToUri($uri)] = Filters::arrayToPath($path);
    }

    /**
     * Create navigation item
     *
     * @param array $request Current request URI
     * @param array $uri URI from current navigation item
     * @param array $metadata Current navigation item's metadata
     * @return array Navigation item
     */
    private function createNavigation($request, $uri, $metadata)
    {
        $data = array();
        if (isset($metadata['hidden']) && $metadata['hidden'] == 'true') {
            $data['hidden'] = true;
        } else {
            $data['title'] = ( isset($metadata['title']) ? $metadata['title'] : end($uri) );
            $data['title'] = ( isset($metadata['navigation']) ? $metadata['navigation'] : $data['title']);
            $data['url'] = Filters::arrayToUri($uri);
            $level = count($uri);
            $data['active'] = ( ( Filters::arrayToUri( array_slice($request, 0, $level) ) == Filters::arrayToUri( $uri ) ) ? true : false );
        }
        return $data;
    }

}
