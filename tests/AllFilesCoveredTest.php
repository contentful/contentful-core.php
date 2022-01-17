<?php

/**
 * This file is part of the contentful/contentful-core package.
 *
 * @copyright 2015-2022 Contentful GmbH
 * @license   MIT
 */

declare(strict_types=1);

namespace Contentful\Tests\Core;

use Contentful\Tests\TestCase;
use Symfony\Component\Finder\Finder;

class AllFilesCoveredTest extends TestCase
{
    /**
     * @dataProvider classFileProvider
     *
     * @param string $file
     */
    public function testAllClassesHaveTestFile($file)
    {
        $file = \str_replace('.php', 'Test.php', $file);
        $this->assertFileExists(__DIR__.'/Unit/'.$file);
    }

    public function classFileProvider()
    {
        $iterator = Finder::create()
            ->files()
            ->name('*.php')
            ->in(__DIR__.'/../src')
        ;

        foreach ($iterator as $file) {
            if ('Interface.php' === \mb_substr($file->getFilename(), -13)) {
                continue;
            }

            yield $file->getFilename() => [$file->getRelativePathname()];
        }
    }
}
