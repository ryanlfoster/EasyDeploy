<?php

/*
 * This file is part of the Deploy package.
 *
 * (c) Cliff Odijk <cliff@obro.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Deploy\Config;

use Deploy\Config;
use Deploy\Json\JsonFile;

class JsonReader implements ReaderInterface
{
	protected $config;
	protected $file;
	
	/**
	 * Set the base information for the reader
	 *
	 * @param Config $config base config object
	 */
	public function __construct(Config $config)
	{
		$this->config = $config;
	}
	
	/**
	 * Set the file and path to read
	 */
	public function setFile($file)
	{
		$this->file = $file;
	}
		
	/**
	 * Read the definition into the $config
	 *
	 * @throws Seld\JsonLint\ParsingException if the json file can't be parserd
	 * @throws Deploy\Json\JsonValidationException if the file can't be validated
	 */
	public function read()
	{
		// Check if we have a file to read
		if (!isset($this->file)){
			throw new \RuntimeException("We din't find a file to read");
		}
		
		// Read the json file
		$file	= new JsonFile($this->file);
		$data	= $file->read();
		$file->validateSchema(__DIR__ . '/../../../res/schema.json');
		
		// Parser data array
		$reader	= new ArrayReader($this->config);
		$reader->setData($data);
		$reader->read();
	}
}