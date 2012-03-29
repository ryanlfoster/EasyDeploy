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
use Deploy\Util\ArrayTo;

class ArrayReader implements ReaderInterface
{
	protected $config;
	protected $data;
	
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
	public function setData($data)
	{
		$this->data = $data;
	}
		
	/**
	 * Read the definition into the $config
	 */
	public function read()
	{
		// Check if we have data to parser
		if (!isset($this->data)){
			throw new \RuntimeException("We din't find a data to read");
		}
		
		// Loop over the deployment variabel to add the different deployments
		if (isset($this->data['deploy']) && is_array($this->data['deploy']) && count($this->data['deploy'])){
			foreach ($this->data['deploy'] as $name => $servers)
			{
				// Define deployment
				$deployment = new Deployment();
				
				// Add a list of servers
				if (is_array($servers)){
					$deployment->addServers($servers);
				}
				// Add a single server
				elseif (is_string($servers)){
					$deployment->addServer($servers);
				}
				
				// Add deployment to the config
				$this->config->addDeployment($name, $deployment);
			}
		}
		
		// Loop over the server variabel to add the different servers
		if (isset($this->data['servers']) && is_array($this->data['servers']) && count($this->data['servers'])){
			foreach ($this->data['servers'] as $name => $params)
			{
				// Add the server to the config
				$server = ArrayTo::Server($params, $this->config);
				$this->config->addServer($name, $server);
			}
		}
		
		// Loop over the events
		if (isset($this->data['events']) && is_array($this->data['events']) && count($this->data['events'])){
			foreach ($this->data['events'] as $name => $events)
			{
				foreach ($events as $params)
				{
					$event = ArrayTo::Event($params, $this->config);
					$this->config->addListner($name, $event);
				}
			}
		}
	}
}