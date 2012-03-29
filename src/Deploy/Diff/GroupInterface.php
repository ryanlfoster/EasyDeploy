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

interface GroupInterface 
{

	/**
	 * Get a list of al the added files
	 *
	 * @return array
	 */
	function getAdded();
	
	/**
	 * Get a list of al the modified files
	 *
	 * @return array
	 */
	function getModified();
	
	/**
	 * Get a list of al the deleted files
	 *
	 * @return array
	 */
	function getDeleted();

}