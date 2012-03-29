<?php

namespace Jcid\Server;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Deploy\Config;
use Deploy\Server\ServerInterface;
use Deploy\Reader\ReaderInterface;

class Db implements ServerInterface
{
	protected $dispatcher;
	protected $config;
	protected $params;
	
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
		echo "Upload diff api\n";
	}

}