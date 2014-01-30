<?php

/**
 * Robodt - Markdown CMS
 * @author      Zomnium
 * @link        http://www.zomnium.com
 * @copyright   2013 Zomnium, Tim van Bergenhenegouwen
 */

namespace Robodt;

interface NodeController
{
	public function __contruct();

	public function create();

	public function save();

	public function get();

	public function find();

	public function delete();

	// CRUD
	// Get
	// Find
	// Save

	// Gateway / Model?
	// Factory make
}
