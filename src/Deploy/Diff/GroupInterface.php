<?php

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