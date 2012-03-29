<?php

/*
 * This file is part of the Deploy package.
 *
 * (c) Cliff Odijk <cliff@obro.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace Deploy\Listner;

use Symfony\Component\EventDispatcher\Event;

abstract class ListnerAbstract
{
	protected $params;
	protected $deploys;
	protected $servers;

	/**
	 * Set the base information for the server
	 *
	 * @param array $params array with params
	 */
	public function __construct($params)
	{
		$this->deploys	= array();
		$this->servers	= array();
		
		if (isset($params['_deploy'])){
			$this->addDeploy($params['_deploy']);
			unset($params['_deploy']);
		}
		
		if (isset($params['_deploys'])){
			$this->addDeploys($params['_deploys']);
			unset($params['_deploys']);
		}
		
		if (isset($params['_server'])){
			$this->addServer($params['_server']);
			unset($params['_server']);
		}
		
		if (isset($params['_servers'])){
			$this->addServers($params['_servers']);
			unset($params['_servers']);
		}
		
		$this->params	= $params;
	}
	
	/**
	 * Add deployment
	 *
	 * @param string $deployment deployment name
	 */
	public function addDeploy($deployment)
	{
		$this->deploys[] = $deployment;
	}
	
	/**
	 * Add deployments
	 *
	 * @param string $deployment deployment name
	 */
	public function addDeploys($deployment)
	{
		$this->deploys = array_merge($this->deploys, $deployment);
	}
	
	/**
	 * Get depoyments
	 * 
	 * @return bool|array
	 */
	public function getDeploys()
	{
		return $this->deploys;
	}
	
	/**
	 * Add server
	 *
	 * @param string $deployment deployment name
	 */
	public function addServer($server)
	{
		$this->servers[] = $server;
	}
	
	/**
	 * Add servers
	 *
	 * @param string $deployment deployment name
	 */
	public function addServers($server)
	{
		$this->servers = array_merge($this->servers, $server);
	}
	
	/**
	 * Get servers
	 * 
	 * @return bool|array
	 */
	public function getServers()
	{
		return $this->servers;
	}
	
	/**
	 * Execute the command
	 * 
	 * @param Event $event
	 */
	abstract public function execute(Event $event);
}
	