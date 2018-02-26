<?php

/**
 * This file is part of the contentful-core.php package.
 *
 * @copyright 2015-2018 Contentful GmbH
 * @license   MIT
 */

namespace Contentful\Tests\Core\Unit\Log;

use Contentful\Core\Log\Timer;
use Contentful\Tests\Core\TestCase;

class TimerTest extends TestCase
{
    public function testInitialState()
    {
        $timer = new Timer();

        $this->assertFalse($timer->isRunning());
        $this->assertNull($timer->getDuration());
    }

    public function testTimerOperation()
    {
        $timer = new Timer();

        $timer->start();
        $this->assertTrue($timer->isRunning());
        $this->assertNull($timer->getDuration());

        $timer->stop();
        $this->assertFalse($timer->isRunning());
        $this->assertInternalType('float', $timer->getDuration());
        $this->assertGreaterThan(0.0, $timer->getDuration());
    }

    public function testTimerCanNotBeRestarted()
    {
        $timer = new Timer();

        $timer->start();
        $timer->stop();
        $timer->start();
        $this->assertFalse($timer->isRunning());
    }

    public function testStoppingBeforeStartingDoesNothing()
    {
        $timer = new Timer();

        $timer->stop();
        $timer->start();
        \sleep(0.1);
        $timer->stop();
        $this->assertFalse($timer->isRunning());
        $this->assertInternalType('float', $timer->getDuration());
        $this->assertGreaterThan(0.0, $timer->getDuration());
    }
}
