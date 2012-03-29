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

class Deployment
{
	protected $servers;
	
	/**
	 * Set the base information for the reader
	 *
	 * @param string $name the name of the deployment
	 */
	public function __construct()
	{
		$this->servers	= array();
	}
	
	/**
	 * Add array of servers
	 *
	 * @param array $servers Add a list of servers
	 */
	public function addServers($servers)
	{
		$this->servers = array_unique(array_merge($this->servers, $servers));
	}
	
	/**
	 * Add a server
	 *
	 * @param string $server server name
	 */
	public function addServer($server)
	{
		$this->servers[]	= $server;
		$this->servers		= array_unique($this->servers);
	}
	
	/**
	 * Return the list of deployment servers
	 *
	 * @param array list of servers
	 */
	public function getServers()
	{
		return $this->servers;
	}
}