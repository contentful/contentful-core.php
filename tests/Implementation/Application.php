<?php

/**
 * This file is part of the contentful/contentful-core package.
 *
 * @copyright 2015-2024 Contentful GmbH
 * @license   MIT
 */

declare(strict_types=1);

namespace Contentful\Tests\Core\Implementation;

use Contentful\Core\Api\ApplicationInterface;

class Application implements ApplicationInterface
{
    /**
     * @var bool
     */
    private $isPackagedApplication;

    public function __construct(bool $isPackagedApplication)
    {
        $this->isPackagedApplication = $isPackagedApplication;
    }

    public function getApplicationName(): string
    {
        return 'the-example-app';
    }

    public function isPackagedApplication(): bool
    {
        return $this->isPackagedApplication;
    }

    public function getApplicationPackageName(): string
    {
        return $this->isPackagedApplication
            ? 'contentful/the-example-app'
            : '';
    }

    public function getApplicationVersion(): string
    {
        return $this->isPackagedApplication
            ? ''
            : '1.0.0';
    }
}
