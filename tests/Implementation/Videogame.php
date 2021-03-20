<?php

/**
 * This file is part of the contentful/contentful-core package.
 *
 * @copyright 2015-2021 Contentful GmbH
 * @license   MIT
 */

declare(strict_types=1);

namespace Contentful\Tests\Core\Implementation;

class Videogame
{
    /**
     * @var string
     */
    private $title = '';

    /**
     * @var string
     */
    private $console = '';

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getConsole(): string
    {
        return $this->console;
    }
}
