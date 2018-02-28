<?php

/**
 * This file is part of the contentful-core.php package.
 *
 * @copyright 2015-2018 Contentful GmbH
 * @license   MIT
 */

namespace Contentful\Tests\Core\Unit\Api\Exception;

use Contentful\Core\Api\Exception;

class BadRequestException extends Exception
{
    public function getBadRequestMessage()
    {
        return 'What kind of request did you send?';
    }
}
