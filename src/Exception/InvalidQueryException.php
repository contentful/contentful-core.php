<?php

/**
 * This file is part of the contentful/contentful-core package.
 *
 * @copyright 2015-2024 Contentful GmbH
 * @license   MIT
 */

declare(strict_types=1);

namespace Contentful\Core\Exception;

use Contentful\Core\Api\Exception;

/**
 * An InvalidQueryException is thrown when the query could not be executed.
 * The most common case is setting a non-existing content type or field name.
 */
class InvalidQueryException extends Exception
{
}
