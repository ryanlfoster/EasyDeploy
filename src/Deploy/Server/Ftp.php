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

use Deploy\Diff\GroupInterface;
use Deploy\Event\FileEvent;

class Ftp extends ServerAbstract
{	
	protected $connection;
	
	/**
	 * Connect to the FTP server
	 *
	 * @throws RuntimeException if there is a connection error
	 */
	protected function connect()
	{
		// Connect to FTP
		if (isset($this->params['port'])){
			$this->connection = @ftp_connect($this->params['server'], $this->params['port'], 30);
		} else {
			$this->connection = @ftp_connect($this->params['server'], 21, 30);
		}
		
		// Check if the connection works
		if (!$this->connection){
			throw new \RuntimeException("No FTP connection could be made");
		}
		
		// Login on FTP
		if (!@ftp_login($this->connection, $this->params['username'], $this->params['password'])){
			throw new \RuntimeException("Could not login to the FTP server");
		}
	}
	
	/**
	 * Disconnect from remote server
	 */
	protected function disconnect()
	{
		if ($this->connection){
			ftp_close($this->connection);
		}
	}
	
	/**
	 * Get file contents from the server
	 *
	 * @param string $file filename + path to remote
	 * 
	 * @return string|bool remote file content
	 */
	protected function getFile($file)
	{
		//Create temp handler: 
		$tempHandle = fopen('php://temp', 'r+'); 
		
		//Get file from FTP: 
		if (@ftp_fget($this->connection, $tempHandle, $this->params['path'].$file, FTP_ASCII, 0)) {
			rewind($tempHandle); 
			$content = trim(stream_get_contents($tempHandle));
			fclose($tempHandle);
			return $content;
		} else { 
			return false; 
		}
	}
	
	/**
	 * Uploads file to remote server
	 *
	 * @param string $file filename + path to remote
	 * @param string $content file content
	 *
	 * @return bool if it is succeed
	 */
	protected function uploadFile($file, $content)
	{
		// Dispatch event
		$fileEvent = new FileEvent($file);
		$this->dispatcher->dispatch("file.upload", $fileEvent);
	
		//Create temp handler: 
		$tempHandle = fopen('php://temp', 'r+'); 
		fwrite($tempHandle, $content);
		rewind($tempHandle); 
		
		//Get file from FTP: 
		if (@ftp_fput($this->connection, $this->params['path'].$file, $tempHandle, FTP_ASCII, 0)) { 
			return true;
		} else { 
			return false; 
		}
	}
	
	/**
	 * Remove remote file
	 * 
	 * @param string $file filename + path to remote
	 *
	 * @return bool if it is succeed
	 */
	protected function removeFile($file)
	{
		// Dispatch event
		$fileEvent = new FileEvent($file);
		$this->dispatcher->dispatch("file.delete", $fileEvent);
	
		//Get file from FTP: 
		if (@ftp_delete($this->connection, $this->params['path'].$file)) { 
			return true;
		} else { 
			return false; 
		}
	}
	
	/**
	 * Recursive create dir
	 *
	 * @param string $dir Full dirname
	 */
	protected function createDir($dir)
	{
		// Check if dir exists
		if (!@ftp_chdir($this->connection, $this->params['path'].$dir)){
			
			// Recursive dir create
			$dirs	= explode("/", $dir);
			$dir	= "";
			
			// Loop over dirs
			foreach ($dirs as $subdir)
			{
				// Check if dir exists
				$newDir = $this->params['path'].$dir.$subdir."/";
				if (!@ftp_chdir($this->connection, $newDir)){
					@ftp_mkdir($this->connection, $newDir);
				}
				
				// Build base dir
				$dir .= $subdir."/";
			}
		}
	}
	
	/**
	 * Remove dir Recursive if it's empty
	 *
	 * @param string $dir Full dirname
	 */
	protected function removeDir($dir)
	{
		// Check if dir exists
		if (@ftp_chdir($this->connection, $this->params['path'].$dir)){
			
			// Recursive dir create
			$dirs	= explode("/", $dir);
			
			// Loop over dirs
			while (count($dirs) > 0)
			{
				$rmdir		= implode("/", $dirs);
				$content	= ftp_nlist($this->connection, $this->params['path'].$rmdir);
				if (count($content) <= 2){
					ftp_rmdir($this->connection, $this->params['path'].$rmdir);
				}
				array_pop($dirs);
			}
		}
	} 

	/**
	 * Deploy the differentials 
	 *
	 * @param GroupInterface $group upload files from inside the diff
	 */
	protected function deploy(GroupInterface $group)
	{
		/**
		 * Get base info
		 */
			$version	= $this->reader->getVersion();
			$files		= array();
			if ($added = $group->getAdded()){
				$files	= array_merge($files, $added);
			}
			if ($added = $group->getModified()){
				$files	= array_merge($files, $added);
			}
		
		/**
		 * Loop over the files
		 */
			if (is_array($files) && count($files)) foreach ($files as $file)
			{
				// Create the dir if it does not exists
				$this->createDir($file->getDir());
				
				// Upload file content
				$content = $this->reader->getFileContent($file->getFile(), $version);
				$this->uploadFile($file->getFile(), $content);
			}
		
		/**
		 * Delete files
		 */
			if ($files = $group->getDeleted()) foreach ($files as $file)
			{
				$this->removeFile($file->getFile());
				$this->removeDir($file->getDir());
			}
			
		/* - */	
	}
		 
}