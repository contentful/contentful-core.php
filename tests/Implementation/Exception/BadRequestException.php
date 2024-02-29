<?php

/**
 * This file is part of the contentful/contentful-core package.
 *
 * @copyright 2015-2024 Contentful GmbH
 * @license   MIT
 */

declare(strict_types=1);

namespace Contentful\Tests\Core\Implementation\Exception;

use Contentful\Core\Api\Exception;

class BadRequestException extends Exception
{
    public function getBadRequestMessage(): string
    {
        return 'What kind of request did you send?';
    }
}
