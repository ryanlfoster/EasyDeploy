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

interface FileInterface 
{

	/**
	 * Returns the modified action
	 * 
	 * @return string
	 */
	function getModifier();

	/**
	 * Return the current file and dir
	 *
	 * @return string
	 */
	function getFile();
	
	/**
	 * Return the file name
	 *
	 * @return string
	 */
	function getFileName();
	
	/**
	 * Return the dir name 
	 *
	 * @return string
	 */
	function getDir(); 
	
}