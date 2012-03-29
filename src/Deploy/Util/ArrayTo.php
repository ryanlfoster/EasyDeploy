<?php

/*
 * This file is part of the Deploy package.
 *
 * (c) Cliff Odijk <cliff@obro.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Deploy\Util;

use Deploy\Config;

class ArrayTo
{
	public static function Server($params, Config $config)
	{
		// Check for the param type
		if (!isset($params['_type'])){
			throw new \RuntimeException("The _type needs to be defined inside the server object");
		}
		$params['_type'] = str_replace("/", "\\", $params['_type']);
		
		// Reflection 
		$class = new \ReflectionClass($params['_type']);
		if (!$class->implementsInterface("Deploy\Server\ServerInterface")){
			throw new \RuntimeException("The server doesn't implement the Deploy\Server\ServerInterface interface");
		} else {
			unset($params['_type']);
			$instance = $class->newInstance($params);
			$instance->setConfig($config);
			return $instance;
		}
	}
	
	public static function Event($params)
	{
		// Check for the param type
		if (!isset($params['_type'])){
			throw new \RuntimeException("The _type needs to be defined inside the server object");
		}
		$params['_type'] = str_replace("/", "\\", $params['_type']);
		
		// Reflection 
		$class	= new \ReflectionClass($params['_type']);
		unset($params['_type']);
		$object	= $class->newInstance($params);
		if ($object instanceof \Deploy\Listner\ListnerAbstract){
			return $object;
		} else {
			throw new \RuntimeException("The event doesn't implement the Deploy\Listner\ListnerAbstract interface");
		}
	}
}