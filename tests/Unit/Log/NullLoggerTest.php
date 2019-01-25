<?php

/**
 * This file is part of the contentful/contentful-core package.
 *
 * @copyright 2015-2019 Contentful GmbH
 * @license   MIT
 */

declare(strict_types=1);

namespace Contentful\Tests\Core\Unit\Log;

use Contentful\Core\Log\NullLogger;
use Contentful\Tests\TestCase;

class NullLoggerTest extends TestCase
{
    public function testLoggerDoesntDoAnything()
    {
        $logger = new NullLogger();

        $logger->debug('Message');
        $logger->info('Message');
        $logger->notice('Message');
        $logger->warning('Message');
        $logger->error('Message');
        $logger->critical('Message');
        $logger->alert('Message');
        $logger->emergency('Message');
        $logger->log('SOME_LEVEL', 'Message');

        $this->markTestAsPassed();
    }
}
