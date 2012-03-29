<?php

/*
 * This file is part of the Deploy package.
 *
 * (c) Cliff Odijk <cliff@obro.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace Deploy\Json;

/**
 * Reads/writes json files.
 *
 * @author Konstantin Kudryashiv <ever.zet@gmail.com>
 * @author Jordi Boggiano <j.boggiano@seld.be>
 */
class JsonFile extends JsonString
{
	private $path;

	/**
 	 * Initializes json file reader/parser.
 	 *
 	 * @param   string  $lockFile   path to a lockfile
 	 */
	public function __construct($path)
	{
		$this->path = $path;
	}

	/**
 	 * Reads json file.
 	 *
 	 * @return  array
 	 */
	public function read()
	{
		try {
			$json = file_get_contents($this->path);
		} catch (\Exception $e) {
			throw new \RuntimeException('Could not read '.$this->path.', you are probably offline ('.$e->getMessage().')');
		}

		return $this->setData($json);
	}
}