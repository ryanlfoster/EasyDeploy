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
use Deploy\Diff\GroupInterface;
use Deploy\Event\ServerEvent;

abstract class ServerAbstract implements ServerInterface
{
	protected $dispatcher;
	protected $config;
	protected $params;
	protected $reader;
	
	/**
	 * Set the base information for the server
	 *
	 * @param array $params array with params
	 */
	public function __construct($params)
	{
		// Add / to end of path
		$params['path'] = rtrim($params['path'], '/').'/';
	
		// Set params
		$this->params = $params;
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
		// Make global
		$this->reader = $reader;
		
		// Connect to the server
		$this->connect();
	
		// Send the events
		$event = new ServerEvent($this);
		$this->dispatcher->dispatch("pre.upload", $event);
		
		// Retrieve the live version id 
		$liveVersion	= trim($this->getFile("deploy.version"));
		$lastVersion	= $reader->getVersion();
		
		// Set the upload 
		if ($liveVersion){
			$diff = $reader->getDiff($liveVersion, $lastVersion);
		} else {
			$diff = $reader->getTree($lastVersion);
		}
		
		// Start deploying 
		if ($diff){
			$this->deploy($diff);
		
			// Save the version on the remote
			$this->uploadFile("deploy.version", $lastVersion);
		}
		
		// Send the events
		$this->dispatcher->dispatch("post.upload", $event);
		
		// Disconnect from server
		$this->disconnect();
	}

	/**
	 * Connect to the remote server
	 */
	abstract protected function connect();
	
	/**
	 * Disconnect from the remote server
	 */
	abstract protected function disconnect();
	
	/**
	 * Get file contents from the server
	 *
	 * @param string $file filename + path to remote
	 * 
	 * @return string|bool remote file content
	 */
	abstract protected function getFile($file);
	
	/**
	 * Uploads file to remote server
	 *
	 * @param string $file filename + path to remote
	 * @param string $content file content
	 *
	 * @return bool if it is succeed
	 */
	abstract protected function uploadFile($file, $content);
	
	/**
	 * Deploy the differentials 
	 *
	 * @param GroupInterface $group upload files from inside the diff
	 */
	abstract protected function deploy(GroupInterface $group);
	
}