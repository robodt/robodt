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
    // Content types
    // Fields

    private $markdown;

    public function __construct()
    {
        $this->markdown = new MarkdownParser;
    }

    /**
     * Parse markdown file
     */
    public function parseFile($file)
    {
        if (is_array($file)) {
            $file = implode(DIRECTORY_SEPARATOR, $file);
        }
        // File found
        if (file_exists($file)) {
            $file = file_get_contents($file, FILE_USE_INCLUDE_PATH);
            $file = $this->splitFile($file);

            // Metadata found
            if (count($file) > 1) {
                return array(
                    'status' => 200,
                    'content' => $this->parseMarkdown($file[1]),
                    'metadata' => $this->parseMetadata($file[0]),
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
     */
    private function splitFile($contents)
    {
        $contents = str_replace("\r", "", $contents);
        return preg_split('![\r\n]+[-]{4,}!i', $contents);
    }

    /**
     * Parse metadata
     */
    private function parseMetadata($contents)
    {
        $metadata = array();
        preg_match_all('/^(.+?):(.+)$/m', $contents, $metadata);
        $metadata = array_combine($metadata[1], $metadata[2]);
        return array_map('trim', $metadata);
    }

    /**
     * Parse markdown content
     */
    public function parseMarkdown($contents)
    {
        return $this->markdown->transformMarkdown($contents);
    }

}