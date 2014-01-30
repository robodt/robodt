<?php

/**
 * Robodt - Markdown CMS
 * @author      Zomnium
 * @link        http://www.zomnium.com
 * @copyright   2013 Zomnium, Tim van Bergenhenegouwen
 */

namespace Robodt;

interface Node
{
	private $data;

	public function __contruct();

	public function render();

	public function get();

	public function set();

	// Construct factory/object
	// Render content
	// Pluggable format rendering: txt, md, html, php?
	// Content type: page, post, custom
	// Get value(s) function
}
