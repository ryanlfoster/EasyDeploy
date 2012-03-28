<?php

namespace Deploy\Servers;

use Deploy\Diff\FileInterface;

interface ServerInterface 
{

	/**
	 * Uploads a file to the server
	 *
	 * @param FileInterface $file the file place holder
	 */
	function uploadFile(FileInterface $file);
	
	/**
	 * Removes a file on the remote server
	 *
	 * @param FileInterface $file the file place holder
	 */
	function removeFile(FileInterface $file);

}