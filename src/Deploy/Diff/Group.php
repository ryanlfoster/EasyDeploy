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

class Group implements GroupInterface
{
	protected $files;

	/**
	 * Group the array of diffs
	 *
	 * @param array $diff list of al the diffs
	 */
	public function __construct($diff)
	{
		if (is_array($diff) && count($diff)) foreach ($diff as $item)
		{
			if ($item instanceof FileInterface)
			{
				$this->files[$item->getModifier()][] = $item;
			}
		}
	}
	
	/**
	 * Get all the added items
	 *
	 * @return array list of FileInterface
	 */
	public function getAdded()
	{
		return $this->get('A');
	}
	
	/**
	 * Get all the modified items
	 *
	 * @return array list of FileInterface
	 */
	public function getModified()
	{
		return $this->get('M');
	}
	
	/**
	 * Get all the deleted items
	 *
	 * @return array list of FileInterface
	 */
	public function getDeleted()
	{
		return $this->get('D');
	}
	
	/**
	 * Get item based on modifier
	 *
	 * @param string $modifier modifier name
	 * 
	 * @return array list of FileInterface
	 */
	public function get($modifier)
	{
		if (isset($this->files[$modifier]) && is_array($this->files[$modifier]) && count($this->files[$modifier]))
		{
			return $this->files[$modifier];
		}
		return false;
	}
}