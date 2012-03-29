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
use Deploy\Event\ServerEvent;
use Deploy\Server\Sftp;

class RemoteExecute extends ListnerAbstract
{	
	protected $commands;

	/**
	 * Set the base information for the server
	 *
	 * @param array $params array with params
	 */
	public function __construct($params)
	{
		parent::__construct($params);
		$this->commands = array();
		
		// Read the command
		if (isset($this->params['command'])){
			$this->commands = array_merge($this->commands, array($this->params['command']));
			unset($this->params['command']);
		}
		
		// Read the commands
		if (isset($this->params['commands'])){
			$this->commands = array_merge($this->commands, $this->params['commands']);
			unset($this->params['commands']);
		}
	}

	/**
	 * Execute event listner on the event
	 *
	 * @param Event $event
	 */
	public function execute(Event $event)
	{
		// Controleren of het een ServerEvent is
		if ( !($event instanceof ServerEvent) ){
			echo "RemoteExecute allen mogelijk met SFTP server\n";
			return;
		}
		
		// Server ophalen en controleren
		$server = $event->getServer();
		if ( !($server instanceof Sftp) ){
			echo "RemoteExecute allen mogelijk met SFTP server\n";
			return;
		}
		
		// Variabelen verzamelen
		$variabelKey	= $variabelValue = array();
		$serverParams	= $server->getParams();
		if ( is_array($serverParams) && count($serverParams) ) foreach ($serverParams as $key => $value)
		{
			$variabelKey[]		= '{{ '.$key.' }}';
			$variabelValue[]	= $value;
		}
		if ( is_array($this->params) && count($this->params) ) foreach ($this->params as $key => $value)
		{
			$variabelKey[]		= '{{ '.$key.' }}';
			$variabelValue[]	= $value;
		}
		
		// Retrieve the SSH connection
		$connection = $server->getConnection();
		
		// Execute commands
		foreach ($this->commands as $command)
		{
			$command = str_replace($variabelKey, $variabelValue, $command);
			print($connection->exec($command));
		}
	}
	
}