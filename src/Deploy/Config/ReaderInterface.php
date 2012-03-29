<?php

/*
 * This file is part of the Deploy package.
 *
 * (c) Cliff Odijk <cliff@obro.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Deploy\Config;

use Deploy\Config;

interface ReaderInterface
{
	/**
	 * Set the base information for the reader
	 *
	 * @param Config $config base config object
	 */
	function __construct(Config $config);
	
	/**
	 * Read the definition into the $config
	 */
	function read();
}