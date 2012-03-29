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

use JsonSchema\Validator;
use Seld\JsonLint\JsonParser;

/**
 * Reads/writes json files.
 *
 * @author Konstantin Kudryashiv <ever.zet@gmail.com>
 * @author Jordi Boggiano <j.boggiano@seld.be>
 * @author Cliff Odijk <cliff@obro.nl>
 */
class JsonString
{
	protected $raw;
	protected $data;
	
	/**
	 * Set the data to parser
	 *
	 * @param string $data the json data
	 */
	public function setData($data)
	{
		$this->raw			= $this->parseJson($data, false);
		return $this->data	= $this->parseJson($data, true);
	}

	/**
 	 * Validates the schema of the current json file according to composer-schema.json rules
 	 *
 	 * @param int $schema a JsonFile::*_SCHEMA constant
 	 * @return Boolean true on success
 	 * @throws \UnexpectedValueException
 	 */
	public function validateSchema($schemaFile)
	{
		$json = file_get_contents($schemaFile);
		
		$parser = new JsonParser();
		$result = $parser->lint($json);
		
		$schemaData = json_decode($json);
		$validator = new Validator();
		$validator->check($this->raw, $schemaData);
		
		if (!$validator->isValid()) {
			$errors = array();
			foreach ((array) $validator->getErrors() as $error) {
				$errors[] = ($error['property'] ? $error['property'].' : ' : '').$error['message'];
			}
			throw new JsonValidationException($errors);
		}

		return true;
	}
	
	/**
 	 * Parses json string and returns hash.
 	 *
 	 * @param string $json json string
 	 *
 	 * @return  mixed
 	 */
	public function parseJson($json, $assoc=false)
	{
		$data = json_decode($json, $assoc);
		if (null === $data && 'null' !== $json) {
			$this->validateSyntax($json);
		}

		return $data;
	}

	/**
 	 * Validates the syntax of a JSON string
 	 *
 	 * @param string $json
 	 * @return Boolean true on success
 	 * @throws \UnexpectedValueException
 	 */
	protected function validateSyntax($json)
	{
		$parser = new JsonParser();
		$result = $parser->lint($json);

		if (null === $result) {
			return true;
		}

		throw $result;
	}

}