<?php

/**
 * This file is part of the contentful/contentful-core package.
 *
 * @copyright 2015-2018 Contentful GmbH
 * @license   MIT
 */

namespace Contentful\Core\Log;

class Timer
{
    /**
     * @var float
     */
    private $startTime = 0.0;

    /**
     * @var float
     */
    private $endTime = 0.0;

    /**
     * @var bool
     */
    private $isStarted = false;

    /**
     * @var bool
     */
    private $isStopped = false;

    /**
     * Starts the timer.
     */
    public function start()
    {
        if ($this->isStarted) {
            return;
        }
        $this->startTime = \microtime(true);
        $this->isStarted = true;
    }

    /**
     * Stops the timer.
     */
    public function stop()
    {
        if ($this->isStopped || !$this->isStarted) {
            return;
        }

        $this->endTime = \microtime(true);
        $this->isStopped = true;
    }

    /**
     * Returns true if the timer is currently running.
     *
     * @return bool
     */
    public function isRunning()
    {
        return $this->isStarted && !$this->isStopped;
    }

    /**
     * Returns the time (in seconds) the timer ran.
     *
     * Returns null if no time has been recorded.
     *
     * @return float|null
     */
    public function getDuration()
    {
        if (!$this->isStarted || !$this->isStopped) {
            return null;
        }

        return $this->endTime - $this->startTime;
    }
}
