<?php

// Title
// URL
// Metadata - class
// Active

// Sub
// Sub sub
// etc

namespace Robodt;

class DebugNavigation
{

	private $navigation;

	public function __construct()
	{
		$this->navigation = array(
			array(
				"title" => "Home",
				"url" => "/",
				"active" => false,
				"items" => false,
				),
			array(
				"title" => "Getting started",
				"url" => "/getting-started",
				"active" => false,
				"items" => false,
				),
			array(
				"title" => "Docs",
				"url" => "/docs",
				"active" => false,
				"items" => false,
				),
			array(
				"title" => "Cases",
				"url" => "/cases",
				"active" => true,
				"items" => array(
					array(
						"title" => "Raax",
						"url" => "/cases/raax",
						"active" => true,
						"items" => false,
						),
					array(
						"title" => "Victas",
						"url" => "/cases/victas",
						"active" => false,
						"items" => false,
						),
					),
				),
			array(
				"title" => "Blog",
				"url" => "/blog",
				"active" => false,
				"items" => false,
				),
			array(
				"title" => "Contact",
				"url" => "/contact",
				"active" => false,
				"items" => false,
				),
			);
	}

	public function items()
	{
		return $this->navigation;
	}

}