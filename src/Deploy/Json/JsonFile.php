<?php

namespace Deploy\Json;

use JsonSchema\Validator;
use Seld\JsonLint\JsonParser;


/**
 * Reads/writes json files.
 *
 * @author Konstantin Kudryashiv <ever.zet@gmail.com>
 * @author Jordi Boggiano <j.boggiano@seld.be>
 */
class JsonFile
{
	const LAX_SCHEMA = 1;
	const STRICT_SCHEMA = 2;

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
	 *
	 */
	public function getPath()
	{
		return $this->path;
	}

	/**
 	 * Checks whether json file exists.
 	 *
 	 * @return  Boolean
 	 */
	public function exists()
	{
		return is_file($this->path);
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

		return static::parseJson($json);
	}

	/**
 	 * Validates the schema of the current json file according to composer-schema.json rules
 	 *
 	 * @param int $schema a JsonFile::*_SCHEMA constant
 	 * @return Boolean true on success
 	 * @throws \UnexpectedValueException
 	 */
	public function validateSchema($schema = self::STRICT_SCHEMA)
	{
		$content = file_get_contents($this->path);
		$data = json_decode($content);

		if (null === $data && 'null' !== $content) {
			self::validateSyntax($content);
		}

		$schemaFile = __DIR__ . '/../../../res/schema.json';
		$schemaData = json_decode(file_get_contents($schemaFile));

		if ($schema === self::LAX_SCHEMA) {
			$schemaData->additionalProperties = true;
			$schemaData->properties->name->required = false;
			$schemaData->properties->description->required = false;
		}

		$validator = new Validator();
		$validator->check($data, $schemaData);

		// TODO add more validation like check version constraints and such, perhaps build that into the arrayloader?

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
	public static function parseJson($json)
	{
		$data = json_decode($json, true);
		if (null === $data && 'null' !== $json) {
			self::validateSyntax($json);
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
	protected static function validateSyntax($json)
	{
		$parser = new JsonParser();
		$result = $parser->lint($json);

		if (null === $result) {
			return true;
		}

		throw $result;
	}

}