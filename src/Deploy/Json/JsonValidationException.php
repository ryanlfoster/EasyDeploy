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

use Exception;

/**
 * 
 */
class JsonValidationException extends Exception
{
    protected $errors;

    public function __construct(array $errors)
    {
        parent::__construct(implode("\n", $errors));
        $this->errors = $errors;
    }

    public function getErrors()
    {
        return $this->errors;
    }
}