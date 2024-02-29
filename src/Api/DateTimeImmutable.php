<?php

/**
 * This file is part of the contentful/contentful-core package.
 *
 * @copyright 2015-2024 Contentful GmbH
 * @license   MIT
 */

declare(strict_types=1);

namespace Contentful\Core\Api;

/**
 * DateTimeImmutable class.
 *
 * This class is used for easier conversion to a timestamp that works with Contentful.
 */
class DateTimeImmutable extends \DateTimeImmutable implements \JsonSerializable
{
    /**
     * Formats the string for an easier interoperability with Contentful.
     */
    public function formatForJson(): string
    {
        $date = $this->setTimezone(new \DateTimeZone('Etc/UTC'));
        $result = $date->format('Y-m-d\TH:i:s');
        $milliseconds = floor($date->format('u') / 1000);

        if ($milliseconds > 0) {
            $result .= '.'.mb_str_pad((string) $milliseconds, 3, '0', \STR_PAD_LEFT);
        }

        return $result.'Z';
    }

    /**
     * Returns a string representation of the current object.
     */
    public function __toString(): string
    {
        return $this->formatForJson();
    }

    /**
     * Returns a JSON representation of the current object.
     */
    public function jsonSerialize(): string
    {
        return $this->formatForJson();
    }
}
