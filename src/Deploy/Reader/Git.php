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

class Git implements ReaderInterface
{
	protected $dispatcher;
	protected $branch;
	protected $version;
	protected $path;
	protected $diffCache;
	protected $treeCache;

	/**
	 * Check if the current directory is a git repo
	 */
	public function isRepo()
	{
		$process = new Process('git rev-parse --git-dir');
		$process->run();
		if (!$process->isSuccessful()) {
			throw new \RuntimeException($process->getErrorOutput());
		}
		$this->path = trim($process->getOutput());
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
		return $this->getPath();
	}
	
	/**
	 * Return the path of the repository
	 *
	 * @return string path to repo dir
	 */
	public function getPath()
	{
		if (!isset($this->path)){
			$this->isRepo();
		}
		return $this->path;
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
		// Check if the current version is already a SHA1
		if ($this->isSha1($this->version)){
			return $this->version;
		}
		
		// Check if the branch is set
		$branch = $this->getBranch();
		if (empty($branch)){
			$branch = 'HEAD';
		}
		
		// Get the current SHA1 of branch
		$process = new Process("git rev-parse ".$branch, $this->getPath());
		$process->run();
		if (!$process->isSuccessful()) {
			throw new \RuntimeException($process->getErrorOutput());
		}
		
		// Return the content
		return $this->version = trim($process->getOutput());
	}
	
	/**
	 * Check if string is valid SHA1 hash
	 *
	 * @param string $str Validated string
	 *
	 * @return bool if string is valid SHA1
	 */
	protected function isSha1($str)
	{
		return (bool) preg_match('/^[0-9a-f]{40}$/i', $str);
	}
	
	/**
	 * Set the branch name 
	 *
	 * @param string $branch The branche name
	 */
	public function setBranch($branch)
	{
		$this->branch = $branch;
	}
	
	/**
	 * Get the current branch name
	 *
	 * @return string branch name
	 */
	public function getBranch()
	{
		// Get the branch of the cache
		if (!empty($this->branch)){
			return $this->branch;
		}
		
		// Get the current SHA1 of branch
		$process = new Process('git branch', $this->getPath());
		$process->run();
		if (!$process->isSuccessful()) {
			throw new \RuntimeException($process->getErrorOutput());
		}
		
		// Parser the branch
		$result = explode("\n", $process->getOutput());
		foreach ($result as $row){
			if ($row[0] == '*'){
				return $this->branch = trim(substr($row, 2));
			}
		}
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
		// Check if the there is a diff
		if ($old == $new){
			return;
		}
	
		// Check if we have done the diff already
		$cacheKey = $old."...".$new;
		if (isset($this->diffCache[$cacheKey])){
			return $this->diffCache[$cacheKey]; 
		}
		
		// Get the diff
		$process = new Process("git diff --name-status --no-renames ".$old." ".$new, $this->getPath());
		$process->run();
		if (!$process->isSuccessful()) {
			throw new \RuntimeException($process->getErrorOutput());
		}
		
		// Changelist ophalen
		$changelist = trim($process->getOutput());
		if (empty($changelist)){
			throw new \RuntimeException("There is no diff between versions");
		}
		$changelist = explode("\n", $changelist);
		
		
		// Build the diff
		$retrun = array();
		foreach ($changelist as $change)
		{
			if (empty($change)) continue;
		
			$item		= explode("\t", $change, 2);
			$retrun[]	= new File($item[0], $item[1]);
		}
		
		// Check file list
		if (is_array($retrun) && count($retrun)){
			
			// Convert and cache the diff
			return $this->diffCache[$cacheKey] = new Group($retrun);
		} else {
			// No files changes
			throw new \RuntimeException("There is no diff between versions");
		}
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
		// Check if we have done the diff already
		if (isset($this->treeCache[$version])){
			return $this->treeCache[$version]; 
		}
		
		// Get the diff
		$process = new Process("git ls-tree --name-only -r ".$version, $this->getPath());
		$process->run();
		if (!$process->isSuccessful()) {
			throw new \RuntimeException($process->getErrorOutput());
		}
		$changelist = explode("\n", trim($process->getOutput()));
		
		// Build the diff
		$retrun = array();
		foreach ($changelist as $change) if (!empty($change))
		{
			$retrun[]	= new File("A", $change);
		}
		
		// Convert and cache the diff
		return $this->treeCache[$version] = new Group($retrun);
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
		// Get the diff
		$process = new Process("git show '".$version.":".$file."'", $this->getPath());
		$process->run();
		if (!$process->isSuccessful()) {
			throw new \RuntimeException($process->getErrorOutput());
		}
		
		// Return the content
		return $process->getOutput();
	}
	
}