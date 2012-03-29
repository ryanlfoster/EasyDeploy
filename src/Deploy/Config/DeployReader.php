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
use Deploy\Json\JsonString;
use Deploy\Reader\ReaderInterface as DeployReaderInterface;

class DeployReader implements ReaderInterface
{
	protected $config;
	protected $reader;
	
	/**
	 * Set the base information for the reader
	 *
	 * @param Config $config base config object
	 */
	public function __construct(Config $config)
	{
		$this->config	= $config;
	}
	
	/**
	 * Set the file and path to read
	 */
	public function setReader(DeployReaderInterface $reader)
	{
		$this->reader = $reader;
	}
		
	/**
	 * Read the definition into the $config
	 *
	 * @throws Seld\JsonLint\ParsingException if the json file can't be parserd
	 * @throws Deploy\Json\JsonValidationException if the file can't be validated
	 */
	public function read()
	{
		// Read file from local config
		$rootFile	= $this->reader->getConfigPath()."/deploy.json";
		if (file_exists($rootFile)){
			$reader = new JsonReader($this->config);
			$reader->setFile($rootFile);
			$reader->read();
		}
		
		try {
			
			// Read content from repo
			$version	= $this->reader->getVersion();
			$content	= $this->reader->getFileContent("deploy.json", $version);
			
			// Read the json file
			$file	= new JsonString();
			$data	= $file->setData($content);
			$file->validateSchema(__DIR__ . '/../../../res/schema.json');
			
			// Parser data array
			$reader	= new ArrayReader($this->config);
			$reader->setData($data);
			$reader->read();
			
		} catch (\Exception $e){}
	}
}