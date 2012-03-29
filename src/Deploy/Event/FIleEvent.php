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

class FileEvent extends Event
{
	protected $file;

	/**
	 * Server event
	 *
	 * @param Server $server the server
	 */
	public function __construct($file)
	{
		$this->file = $file;
	}
	
	/**
	 * Return the file
	 *
	 * @return filname 
	 */
	public function getFile()
	{
		return $this->file;
	}
}