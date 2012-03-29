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

interface ServerInterface 
{

	/**
	 * Set the base information for the server
	 *
	 * @param array $params array with params
	 */
	function __construct($params);
	
	/**
	 * Return the server params
	 *
	 * @return array of server param
	 */
	function getParams();
	
	/**
	 * Register the event dispatcher
	 *
	 * @param EventDispatcherInterface $dispatcher the event dispatcher
	 */
	function setDispatcher(EventDispatcherInterface $dispatcher);
	
	/**
	 * Register the config object
	 *
	 * @param Config $config the config object
	 */
	function setConfig(Config $config);
	
	/**
	 * Start uploading differentials
	 *
	 * @param ReaderInterface $reader Reader interface to get the data
	 */
	function uploadDiff(ReaderInterface $reader);
	
}