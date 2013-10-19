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


	public function getTree($dir) {
		if (is_array($dir)) {
			$dir = implode(DIRECTORY_SEPARATOR, $dir);
		}
		$dir = new DirectoryIterator($dir);
		return $this->generateTree($dir);
	}


	private function generateTree( DirectoryIterator $dir ) {
		$data = array();
		foreach ( $dir as $node ) {
			if ( $node->isDir() && !$node->isDot() ) {
				$data[$node->getFilename()] = $this->generateTree( new DirectoryIterator( $node->getPathname() ) );
			}
			else if ( $node->isFile() ) {
				if ( substr( $node->getFilename(), 0, 1) != '.') {
					$data[] = $node->getFilename();
				}
			}
		}
		return $data;
	}


	private function splitFile($contents) {
		$contents = str_replace("\r", "", $contents);
		return preg_split('![\r\n]+[-]{4,}!i', $contents);
	}


	private function parseMetadata($contents) {
		// Generate array from string
		$metadata = array();
		preg_match_all('/^(.+?):(.+)$/m', $contents, $metadata);

		// Convert array to key value format
		$metadata = array_combine($metadata[1], $metadata[2]);

		// Trim values and return
		return array_map('trim', $metadata);
	}


}