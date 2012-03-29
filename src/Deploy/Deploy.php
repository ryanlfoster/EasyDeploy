<?php

/*
 * This file is part of the Deploy package.
 *
 * (c) Cliff Odijk <cliff@obro.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Deploy;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\Event;
use Deploy\Event\EventDispatcher;
use Deploy\Event\ConfigEvent;
use Deploy\Config\JsonReader;
use Deploy\Config\DeployReader;
use Deploy\Listner\InfoSubscriber;
use Deploy\Reader\ReaderInterface;

class Deploy
{
	protected $reader;
	protected $output;
	protected $config;
		
	/**
	 * Handel the base deployment
	 *
	 * @param ReaderInterface $reader reader interface object
	 */
	public function __construct(ReaderInterface $reader, OutputInterface $output)
	{
		// Base data
		$this->reader		= $reader;
		$this->output		= $output;
		
		// Events
		$this->dispatcher	= new EventDispatcher();
		$this->dispatcher->addSubscriber(new InfoSubscriber($output));
		$this->reader->setDispatcher($this->dispatcher);
	}
	
	/**
	 * Run the deployment for a specific server
	 *
	 * @param string $server Server alias to run
	 */
	public function server($server)
	{
		// Get the branch
		$branch = $this->preRun();
		
		// Check if there is a server specified
		$this->runServer($branch, $server);
		
		// Post running
		$this->postRun();
	}
	
	/**
	 * Upload to all servers
	 */
	public function all()
	{
		// Get the branch
		$branch = $this->preRun();
		
		// Loop over servers
		$servers	= $this->config->getServers();
		foreach ($servers as $server => $object)
		{
			$this->runServer($branch, $server);
		}
		
		// Post running
		$this->postRun();
	}

	/**
	 * Run the deployment tool
	 */
	public function run()
	{
		// Get the branch
		$branch = $this->preRun();
		
		// Check if there is a deployment defined for the branch
		if ($this->config->hasDeployment($branch))
		{
			// Get the deployment
			$deployment = $this->config->getDeployment($branch);
			$servers	= $deployment->getServers();
			
			// Loop over servers
			foreach ($servers as $server)
			{
				$this->runServer($branch, $server);
			}
		} else {
			$this->output->writeln('<info>No output deployment defined</info>');
		}
		
		// Post running
		$this->postRun();
	}
	
	/**
	 * Check if we can read the deployment from the base
	 */
	protected function getConfig()
	{
		// Basic config object
		$config		= new Config();
		
		// Read config file from current directory
		$rootFile	= "deploy.json";
		if (file_exists($rootFile)){
			$reader = new JsonReader($config);
			$reader->setFile($rootFile);
			$reader->read();
		}
		
		// Read config from reader
		$reader = new DeployReader($config);
		$reader->setReader($this->reader);
		$reader->read();
		
		// Add global events
		$this->dispatcher->addListeners($config->getListners());
		$this->dispatcher->dispatch("config", new ConfigEvent($config));
		
		// Return the config
		return $config;
	}

	/**
	 * Run pre deployment proces
	 * 
	 * @return string branch name
	 */
	protected function preRun()
	{
		// Get the config and branch
		$this->config 	= $this->getConfig();
		$branch			= $this->reader->getBranch();
		
		// Add event listners based on deployment
		$this->dispatcher->addListeners($this->config->getListners($branch));
		$this->dispatcher->dispatch("pre.run");
		
		// Return branch name
		return $branch;
	}
	
	/**
	 * Post running proces
	 */
	protected function postRun()
	{
		// Dispatch post run
		$this->dispatcher->dispatch("post.run");
	}

	/**
	 * Run the server uploader
	 * 
	 * @param string $branch The branch name
	 * @param string $server The server name
	 */
	protected function runServer($branch, $server)
	{
		// Check if config server exists
		if ($this->config->hasServer($server))
		{
			// Listners ophalen
			$serverEvents = array_merge($this->config->getListners($branch, $server),
										$this->config->getListners(null, $server));
			$this->dispatcher->addListeners($serverEvents);
		
			// Get the server and start uploading
			$serverConfig	= $this->config->getServer($server);
			$serverConfig->setDispatcher($this->dispatcher);
			$serverConfig->uploadDiff($this->reader);
			
			// Clear listners
			$this->dispatcher->removeListeners($serverEvents);
			
		} else {
			throw new \RuntimeException("Server ".$server." not found");
		}
	}
	 
	
}