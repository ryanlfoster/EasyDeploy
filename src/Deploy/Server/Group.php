<?php

/*
 * This file is part of the Deploy package.
 *
 * (c) Cliff Odijk <cliff@obro.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace Deploy\Server;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Deploy\Config;
use Deploy\Reader\ReaderInterface;
use Deploy\Util\ArrayTo;

class Group implements ServerInterface
{
	protected $dispatcher;
	protected $config;
	protected $params;
	protected $servers;
	
	/**
	 * Set the base information for the server
	 *
	 * @param array $params array with params
	 */
	public function __construct($params)
	{
		$this->params	= $params;
	}
	
	/**
	 * Return the server params
	 *
	 * @return array of server param
	 */
	public function getParams()
	{
		return $this->params;
	}
	
	/**
	 * Register the event dispatcher
	 *
	 * @param EventDispatcherInterface $dispatcher the event dispatcher
	 */
	public function setDispatcher(EventDispatcherInterface $dispatcher)
	{
		$this->dispatcher = $dispatcher;
	}
	
	/**
	 * Register the config object
	 *
	 * @param Config $config the config object
	 */
	public function setConfig(Config $config)
	{
		$this->config = $config;
	}
	
	/**
	 * Start uploading differentials
	 *
	 * @param ReaderInterface $reader Reader interface to get the data
	 */
	public function uploadDiff(ReaderInterface $reader)
	{
		// Convert params to servers
		if (is_array($this->params['servers']) && count($this->params['servers'])){
			foreach ($this->params['servers'] as $server)
			{
				if (is_string($server)){
					$server = $this->config->getServer($server);
				} else {
					$server = ArrayTo::Server($server);
				}
				
				$server->setDispatcher($this->dispatcher);
				$server->uploadDiff($reader);
			}
		}
	}
	
}