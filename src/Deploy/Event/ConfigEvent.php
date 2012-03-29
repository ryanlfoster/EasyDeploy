<?php

/*
 * This file is part of the Deploy package.
 *
 * (c) Cliff Odijk <cliff@obro.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace Deploy\Event;

use Symfony\Component\EventDispatcher\Event;
use Deploy\Config;

class ConfigEvent extends Event
{
	protected $config;

	/**
	 * Server event
	 *
	 * @param Server $server the server
	 */
	public function __construct(Config $config)
	{
		$this->config = $config;
	}
	
	/**
	 * Return the config
	 *
	 * @return Config 
	 */
	public function getConfig()
	{
		return $this->config;
	}
}