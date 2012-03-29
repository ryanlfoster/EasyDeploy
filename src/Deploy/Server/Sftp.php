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

class Sftp extends ServerAbstract
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
			$this->connection = new \Net_SFTP($this->params['server'], $this->params['port']);
		} else {
			$this->connection = new \Net_SFTP($this->params['server']);
		}
		
		// Check for the auth type
		if (!isset($this->params['_auth'])){
			throw new \RuntimeException("No SSH _auth method defined");
		}
		
		// Login to server
		switch ($this->params['_auth'])
		{
			case "none";
				$login = $this->connection->login($this->params['username']);
			break;
			case "password":
				$login = $this->connection->login($this->params['username'], $this->params['password']);
			break;
			case "pubkey":
				$key = new Crypt_RSA();
				$key->loadKey(file_get_contents('privkeyfile'));
				if (isset($this->params['password'])){
					$key->setPassword($this->params['password']);
				}
				$login = $this->connection->login($this->params['username'], $key);
			break;
			default:
				throw new \RuntimeException("No valid SSH _auth method defined only none, password and pubkey");
			break;
		}
		
		// Check the login
		if ($login == false){
			throw new \RuntimeException("Can't login");
		}
	}
	
	/**
	 * Disconnect from remote server
	 */
	protected function disconnect()
	{
		if ($this->connection){
			unset($this->connection);
		}
	}
	
	/**
	 * Return the connection object for remote stuff
	 * 
	 * @return Net_SFTP connection object
	 */
	public function getConnection()
	{
		return $this->connection;
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
		return $this->connection->get($this->params['path'].$file);
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
	
		// Upload file
		if ($this->connection->stat($this->params['path'].$file)){
			$this->removeFile($file);
		}
		return $this->connection->put($this->params['path'].$file, $content);
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
	
		// Remove file
		return $this->connection->delete($this->params['path'].$file);
	}

	/**
	 * Recursive create dir
	 *
	 * @param string $dir Full dirname
	 */
	protected function createDir($dir)
	{
		if (!$this->connection->chdir($this->params['path'].$dir))
		{
			// Recursive dir create
			$dirs	= explode("/", $dir);
			$dir	= "";
			
			// Loop over dirs
			foreach ($dirs as $subdir)
			{
				// Check if dir exists
				$newDir = $this->params['path'].$dir.$subdir."/";
				if (!$this->connection->chdir($newDir)){
					$this->connection->mkdir($newDir);
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
		if ($this->connection->chdir($this->params['path'].$dir))
		{	
			// Recursive dir create
			$dirs	= explode("/", $dir);
			
			// Loop over dirs
			while (count($dirs) > 0)
			{
				$rmdir		= implode("/", $dirs);
				$content	= $this->connection->nlist($this->params['path'].$rmdir);
				if (count($content) <= 2){
					$this->connection->rmdir($this->params['path'].$rmdir);
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