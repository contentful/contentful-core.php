<?php

/**
 * This file is part of the contentful/contentful-core package.
 *
 * @copyright 2015-2018 Contentful GmbH
 * @license   MIT
 */

namespace Contentful\Tests\Core\Unit\Log;

use Contentful\Core\Log\NullLogger;
use Contentful\Tests\Core\TestCase;

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
