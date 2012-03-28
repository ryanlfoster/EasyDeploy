<?php

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
	
	/**
	 * Get the data of the file to upload
	 *
	 * @return mixed
	 */
	function getData();

	
}