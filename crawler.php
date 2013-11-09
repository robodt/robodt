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

class Crawler {

    protected $content;
    protected $dir;
    protected $index;
    // protected $cache;

    public function __construct()
    {
        $this->content = new Content;
    }

    public function indexContent($dir, $request = array())
    {
        $this->dir = Filters::arrayToPath($dir);
        $dir = $this->createDirectoryObject($dir);
        $data = $this->contentCrawler($dir, $request);
        $data['index'] = $this->index;
        // $data['cache'] = $this->cache;
        return $data;
    }

    private function createDirectoryObject($dir)
    {
        return new DirectoryIterator( ( is_array($dir) ? Filters::arrayToPath($dir) : $dir ) );
    }

    private function contentCrawler( DirectoryIterator $dir, $request, $uri = array(), $path = array() )
    {
        $data = array();

        $current_metadata = $this->getMetadata($path);

        if ($current_metadata) {

            $current_uri = $uri;
            $current_uri[] = $this->createUri($path, $current_metadata);

            $this->createIndex($path, $current_uri);

            $current_navigation = $this->createNavigation( $request, $current_uri, $current_metadata );
        }

        foreach ($dir as $node) {

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

            } else if ($node->isFile() && substr($node->getFilename(), 0, 1) != '.') {
                $data['tree'][] = $node->getFilename();
            }

            // http://us2.php.net/manual/en/function.array-diff-assoc.php
            // $this->cache[ Filters::arrayToPath( array($node->getPathname(), $node->getFilename()) ) ] = $node->getCTime();
        }

        $current_navigation['items'] = ( ( count($current_navigation['items']) > 0 ) ? $current_navigation['items'] : false );
        $data['navigation'] = ( ( count($path) > 0 ) ? $current_navigation : $current_navigation['items'] );

        return $data;
    }

    private function getMetadata($path)
    {
        $file = array();
        $file[] = $this->dir;
        if ( count($path) > 0 ) $file = array_merge($file, $path);
        $file[] = 'index.txt';
        $file = $this->content->parseFile( $file );
        return ( ( $file['status'] == 200 ) ? $file['metadata'] : false );
    }

    private function createUri($path, $metadata)
    {
        if ($metadata && isset($metadata['url'])) {
            return $metadata['url'];
        } else {
            return Filters::pathToUri(end($path));
        }
    }

    private function createIndex($path, $uri)
    {
        $path[] = 'index.txt';
        $this->index[Filters::arrayToUri($uri)] = Filters::arrayToPath($path);
    }

    private function createNavigation($request, $uri, $metadata)
    {
        $data = array();
        $data['title'] = ( isset($metadata['title']) ? $metadata['title'] : end($uri) );
        $data['title'] = ( isset($metadata['navigation']) ? $metadata['navigation'] : $data['title']);
        $data['url'] = Filters::arrayToUri($uri);
        $level = count($uri);
        $data['active'] = ( ( Filters::arrayToUri( array_slice($request, 0, $level) ) == Filters::arrayToUri( $uri ) ) ? true : false );
        return $data;
    }

}
