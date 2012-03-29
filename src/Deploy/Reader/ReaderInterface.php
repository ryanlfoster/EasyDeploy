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

interface ReaderInterface
{
	
	/**
	 * Check if current dir is repository
	 *
	 * @throws \RuntimeException if the current directory is not a git repository
	 */
	function isRepo();
	
	/**
	 * Set the current version of the repository / commit
	 *
	 * @param string @version the current version of the commit
	 */
	function setVersion($version);
	
	/**
	 * Get the current version of the repository / commit
	 *
	 * @return string the current version of the commit
	 */
	function getVersion();
	
	/**
	 * Register the event dispatcher
	 *
	 * @param EventDispatcherInterface $dispatcher the event dispatcher
	 */
	function setDispatcher(EventDispatcherInterface $dispatcher);
	
	/**
	 * Get the config for the deployment 
	 *
	 * @return Deploy\Config
	 */
	function getConfigPath();
	
	/**
	 * Return the path to the repos
	 *
	 * @return string
	 */
	function getPath();
	
	/**
	 * Returns the current working branch 
	 *
	 * @throws \RuntimeExeption if there is no branch defined
	 *
	 * @return string with the branch name
	 */
	function getBranch();
	
	/**
	 * Get the diff based on the old and new
	 * 
	 * @param string $old the old version
	 * @param string $new the new version
	 *
	 * @return Deploy\Diff\GroupInterface
	 */
	function getDiff($old, $new);
	
	/**
	 * Get the full tree based on a revision
	 *
	 * @param string $version the version number
	 *
	 * @return Deploy\Diff\GroupInterface
	 */
	function getTree($version);
	
	/**
	 * Get file content from revision
	 *
	 * @param string $version the version number
	 * @param string $file the full filename
	 *
	 * @return string the file content
	 */
	function getFileContent($file, $version);
	
}