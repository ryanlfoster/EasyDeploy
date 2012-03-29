<?php

/*
 * This file is part of the Deploy package.
 *
 * (c) Cliff Odijk <cliff@obro.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace Deploy\Reader;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Process\Process;
use Deploy\Diff\File;
use Deploy\Diff\Group;
use Deploy\Event\ConfigEvent;

class Svn implements ReaderInterface
{
	protected $dispatcher;
	protected $branch;
	protected $version;
	protected $path;
	protected $diffCache;

	/**
	 * Check if the current directory is a git repo
	 */
	public function isRepo()
	{
		$process = new Process('svnlook uuid .');
		$process->run();
		if (!$process->isSuccessful()) {
			throw new \RuntimeException($process->getErrorOutput());
		}
		return true;
	}

	/**
	 * Register the event dispatcher
	 *
	 * @param EventDispatcherInterface $dispatcher the event dispatcher
	 */
	public function setDispatcher(EventDispatcherInterface $dispatcher)
	{
		$this->dispatcher = $dispatcher;
	}
	
	/**
	 * Return the reader config path
	 *
	 * @return string with the config path
	 */
	public function getConfigPath()
	{
		return $this->getPath()."/conf/";
	}
	
	/**
	 * Return the path of the repository
	 *
	 * @return string path to repo dir
	 */
	public function getPath()
	{
		return ".";
	}
	
	/**
	 * Set the current version of the repository / commit
	 *
	 * @param string @version the current version of the commit
	 */
	public function setVersion($version)
	{
		$this->version = $version;
	}
	
	/**
	 * Get the current version of the repository / commit
	 *
	 * @return string the current version of the commit
	 */
	public function getVersion()
	{
		return $this->version;
	}
	
	/**
	 * Get the current branch name
	 *
	 * @return string branch name
	 */
	public function getBranch()
	{
		// If we already have the branch
		if (isset($this->branch)){
			return $this->branch;
		}
		
		// Find the branch
		$process = new Process('svnlook dirs-changed '.$this->getPath());
		$process->run();
		if (!$process->isSuccessful()) {
			throw new \RuntimeException($process->getErrorOutput());
		}
		
		// Check the results
		$directorys = explode("\n", trim($process->getOutput()));
		foreach ($directorys as $directory)
		{
			// If the root is changed continue
			if ($directory == '/'){
				continue;
			}
			
			// Check for the trunk
			elseif (substr($directory, 0, 5) == 'trunk'){
				return $this->branch = 'trunk';
			}
			
			// Check for the branches folder
			elseif (substr($directory, 0, 8) == 'branches'){
				$branche = explode("/", $directory, 3);
				if (isset($branche[1]) && !empty($branche[1])){
					return $this->branch = $branche[1];
				}
			}
			
			// Check for the tag folder
			elseif (substr($directory, 0, 4) == 'tags'){
				continue;
			}
			
		}
		throw new \RuntimeException("No branch detected in changeset");
	}
	
	/**
	 * Get the diff based on the old and new
	 * 
	 * @param string $old the old version
	 * @param string $new the new version
	 *
	 * @return Deploy\Diff\GroupInterface
	 */
	public function getDiff($old, $new)
	{
		// Check if we have done the diff already
		$cacheKey = $old."...".$new;
		if (isset($this->diffCache[$cacheKey])){
			return $this->diffCache[$cacheKey]; 
		}
		
		// Get the diff
		$branche	= $this->getBranch();
		$path		= realpath($this->getPath())."/".($branche!='trunk'?'branches/':'').$branche."/";
		$process	= new Process("svn --summarize --non-interactive --config-dir /tmp diff 'file://".$path."@".$old."' 'file://".$path."@".$new."'");
		$process->run();
		if (!$process->isSuccessful()) {
			throw new \RuntimeException($process->getErrorOutput());
		}
		$changelist = explode("\n", trim($process->getOutput()));
		
		// Build the diff
		$retrun = array();
		foreach ($changelist as $change)
		{
			$item		= explode("       ", $change, 2);
			$item[1]	= str_replace("file://".$path, "", $item[1]);
			$retrun[]	= new File($item[0], $item[1]);
		}
		
		// Convert and cache the diff
		return $this->diffCache[$cacheKey] = new Group($retrun);
	}
	
	/**
	 * Get the full tree based on a revision
	 *
	 * @param string $version the version number
	 *
	 * @return Deploy\Diff\GroupInterface
	 */
	public function getTree($version)
	{
	
	}
	
	/**
	 * Get file content from revision
	 *
	 * @param string $version the version number
	 * @param string $file the full filename
	 *
	 * @return string the file content
	 */
	public function getFileContent($file, $version)
	{
	
	}
}