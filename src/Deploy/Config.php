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

use Deploy\Config;
use Deploy\Server\ServerInterface;
use Deploy\Listner\ListnerAbstract;

class Config
{
	protected $deploys;
	protected $servers;
	protected $listners;
	
	/**
	 * Define the defaults
	 */
	public function __construct()
	{
		$this->deploys	= array();
		$this->servers	= array();
		$this->listners	= array();
	}
	
	/**
	 * Add deployment to the config array
	 *
	 * @param Config\Deployment $server server config object
	 */
	public function addDeployment($name, Config\Deployment $deploy)
	{
		$this->deploys[$name] = $deploy;
	}
	
	/**
	 * Check if deployment exists
	 *
	 * @param string $name The deployment name
	 * 
	 * @return bool 
	 */
	public function hasDeployment($name)
	{
		if (!isset($this->deploys[$name])){
			return false;
		}
		return true;
	}
	
	/**
	 * Get a deployment config by name
	 *
	 * @param string $name The name of the server
	 *
	 * @return Config\Deployment object
	 */
	public function getDeployment($name)
	{
		if (!$this->hasDeployment($name)){
			throw new \RuntimeException("Deployment with name ".$name." not found");
		}
		return $this->deploys[$name];
	}
	
	/**
	 * Get all registerd deployments
	 * 
	 * @return array with Config\Deployment
	 */
	public function getDeployments()
	{
		return $this->deploys;
	}
	
	/**
	 * Add server to the config array
	 *
	 * @param ServerInterface $server server config object
	 */
	public function addServer($name, ServerInterface $server)
	{
		$this->servers[$name] = $server;
	}
	
	/**
	 * Check if servers exists
	 *
	 * @param string $name The server name
	 * 
	 * @return bool 
	 */
	public function hasServer($name)
	{
		if (!isset($this->servers[$name])){
			return false;
		}
		return true;
	}
	
	/**
	 * Get a server config by name
	 *
	 * @param string $name The name of the server
	 *
	 * @return ServerInterface object
	 */
	public function getServer($name)
	{
		if (!$this->hasServer($name)){
			throw new \RuntimeException("Server with name ".$name." not found");
		}
		return $this->servers[$name];
	}
	
	/**
	 * Get all registerd servers
	 * 
	 * @return array with servers
	 */
	public function getServers()
	{
		return $this->servers;
	}
	
	/**
	 * Add server to the config array
	 *
	 * @param ServerInterface $server server config object
	 */
	public function addListner($name, ListnerAbstract $listner)
	{
		$this->listners[$name][] = $listner;
	}
	
	/**
	 * Get global events
	 */
	public function getListners($deploy=null, $server=null)
	{
		$return = array();
		foreach ($this->listners as $name => $listners)
		{
			foreach ($listners as $listner)
			{
				$deployments	= $listner->getDeploys();
				$servsers		= $listner->getServers();
				
				if ( 
					( ( $deploy == null && !$deployments ) || ( $deploy != null && in_array($deploy, $deployments) ) ) &&
					( ( $server == null && !$servsers ) || ( $server != null && in_array($server, $servsers) ) )
				)	
				{
					$return[$name][] = array($listner, 'execute');
				}
			}
		}
		return $return;
	}
}