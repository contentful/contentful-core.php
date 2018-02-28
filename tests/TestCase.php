<?php

/**
 * This file is part of the contentful-core.php package.
 *
 * @copyright 2015-2018 Contentful GmbH
 * @license   MIT
 */

namespace Contentful\Tests\Core;

abstract class TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * Creates an empty assertion (true == true).
     * This is done to mark tests that are expected to simply work (i.e. not throw exceptions).
     * As PHPUnit does not provide convenience methods for marking a test as passed,
     * we define one.
     */
    protected function markTestAsPassed()
    {
        $this->assertTrue(true, 'Test case did not throw an exception and passed.');
    }

    /**
     * @param string $file
     * @param object $object
     * @param string $message
     */
    protected function assertJsonFixtureEqualsJsonObject($file, $object, $message = '')
    {
        $dir = $this->convertClassToFixturePath(\debug_backtrace()[1]['class']);

        $this->assertJsonStringEqualsJsonFile($dir.'/'.$file, \GuzzleHttp\json_encode($object), $message);
    }

    /**
     * @param string $file
     * @param string $string
     * @param string $message
     */
    protected function assertJsonFixtureEqualsJsonString($file, $string, $message = '')
    {
        $dir = $this->convertClassToFixturePath(\debug_backtrace()[1]['class']);

        $this->assertJsonStringEqualsJsonFile($dir.'/'.$file, $string, $message);
    }

    /**
     * @param string $file
     *
     * @return string
     */
    protected function getFixtureContent($file)
    {
        $dir = $this->convertClassToFixturePath(\debug_backtrace()[1]['class']);

        return \file_get_contents($dir.'/'.$file);
    }

    /**
     * This automatically determined where to store the fixture according to the test name.
     * For instance, it will convert a the class
     * Contentful\Tests\Core\Unit\Api\BaseClient
     * to __DIR__.'/Fixtures/Unit/Api/BaseClient/'.$file.
     *
     * @param string $class
     *
     * @return string
     */
    private function convertClassToFixturePath($class)
    {
        $class = \str_replace(__NAMESPACE__.'\\', '', $class);
        $class = \str_replace('\\', '/', $class);
        $class = \mb_substr($class, 0, -4);

        return __DIR__.'/Fixtures/'.$class;
    }
}
