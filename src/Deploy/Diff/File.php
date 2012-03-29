<?php

/*
 * This file is part of the Deploy package.
 *
 * (c) Cliff Odijk <cliff@obro.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Deploy\Diff;

class File implements FileInterface
{
	protected $modified;
	protected $file;
	protected $filename;
	protected $dir;

	/**
	 * Build modified item
	 *
	 * @param string $modified Modifier
	 * @param string $file Full filename
	 */
	public function __construct($modified, $file)
	{
		$this->modified	= $modified;
		$this->file		= $file;
		
		$pathinfo		= pathinfo($this->file);
		$this->filename	= $pathinfo['basename'];
		$this->dir		= $pathinfo['dirname'];
	}
	
	/**
	 * Return the modifie
	 *
	 * @param string modifier
	 */
	public function getModifier()
	{
		return $this->modified;
	}
	
	/**
	 * Return the current file and dir
	 *
	 * @return string
	 */
	public function getFile()
	{
		return $this->file;
	}
	
	/**
	 * Return the file name
	 *
	 * @return string
	 */
	public function getFileName()
	{
		return $this->filename;
	}
	
	/**
	 * Return the dir name 
	 *
	 * @return string
	 */
	public function getDir()
	{
		return $this->dir;
	}
}