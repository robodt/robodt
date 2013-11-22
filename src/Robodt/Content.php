<?php

/**
 * Robodt - Markdown CMS
 * @author      Zomnium
 * @link        http://www.zomnium.com
 * @copyright   2013 Zomnium, Tim van Bergenhenegouwen
 */

namespace Robodt;

use dflydev\markdown\MarkdownParser as MarkdownParser;

class Content
{
    private $markdown;
    private $index;
    private $register;

    public function __construct()
    {
        $this->markdown = new MarkdownParser;
        $this->index = array();
        $this->register = array();
    }

    /**
     * Render content register
     *
     * @param array $filetree File tree of content folder
     * @param string $root Root directory of content folder
     * @return array Content register
     */
    public function renderContent($uri, $dir)
    {
    	$register = array();

    	foreach ($filetree as $key => $value) {
    		if (is_array($key)) {
    			$this->renderContent($key, $prefix . '/' . $key);
    		} else if ($key == 'index.txt') {
    			
    		}
    	}
    }

    /**
     * Parse markdown file
     *
     * @param array or string $file Absolute path to file
     * @return array Result of rendering with statuscode, content and metadata
     */
    public function parseFile($file)
    {
        $file = Filters::arrayToPath($file);

        // File found
        if (file_exists($file)) {
            $file = file_get_contents($file, FILE_USE_INCLUDE_PATH);
            $file = $this->splitFile($file);

            // Metadata found
            if (count($file) > 1) {
                $content = $this->parseMarkdown($file[1]);
                $metadata = $this->parseMetadata($file[0]);
                $status = ( isset($metadata['status']) ? $metadata['status'] : 200 );
                return array(
                    'status' => $status,
                    'content' => $content,
                    'metadata' => $metadata,
                    );

            // No metadata found
            } else {
                return array(
                    'status' => 200,
                    'content' => $this->parseMarkdown($file[0]),
                    );
            }

        // File not found, return false
        } else {
            return array(
                'status' => 404,
                'content' => false,
                );
        }
    }

    /**
     * Split metadata from content
     *
     * @param string $contents File content
     * @return array Content in pieces
     */
    private function splitFile($contents)
    {
        $contents = str_replace("\r", "", $contents);
        return preg_split('![\r\n]+[-]{4,}!i', $contents);
    }

    /**
     * Parse metadata
     *
     * @param string $contents Content metadata
     * @return array Metadata in key value format
     */
    private function parseMetadata($contents)
    {
        $metadata = array();
        preg_match_all('/^(.+?):(.+)$/m', $contents, $metadata);
        $metadata = array_combine($metadata[1], $metadata[2]);
        return array_map('trim', $metadata);
    }

    /**
     * Parse markdown content to html
     * 
     * @param string $contents Markdown content
     * @return string Html content
     */
    public function parseMarkdown($contents)
    {
        return $this->markdown->transformMarkdown($contents);
    }

}