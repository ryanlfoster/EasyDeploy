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
use Deploy\Server\ServerInterface;

class ServerEvent extends Event
{
	protected $server;

	/**
	 * Server event
	 *
	 * @param Server $server the server
	 */
	public function __construct(ServerInterface $server)
	{
		$this->server = $server;
	}
	
	/**
	 * Return the server
	 *
	 * @return Server 
	 */
	public function getServer()
	{
		return $this->server;
	}
}