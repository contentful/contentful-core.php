<?php

/**
 * This file is part of the contentful/contentful-core package.
 *
 * @copyright 2015-2018 Contentful GmbH
 * @license   MIT
 */

namespace Contentful\Core\Api;

/**
 * BaseQuery class.
 *
 * A BaseQuery is used to filter and order collections when making API requests.
 */
abstract class BaseQuery
{
    /**
     * ISO8601 but with the seconds forced to 0.
     *
     * @var string
     */
    const DATE_FORMAT = 'Y-m-d\TH:i:00P';

    /**
     * Maximum number of results to retrieve.
     *
     * @var int|null
     */
    private $limit;

    /**
     * The first result to retrieve.
     *
     * @var int|null
     */
    private $skip;

    /**
     * For entries, limit results to this content type.
     *
     * @var string|null
     */
    private $contentType;

    /**
     * Assets only. Limit to a group of MIME-types.
     *
     * @var string|null
     */
    private $mimeTypeGroup;

    /**
     * List of fields to order by.
     *
     * @var array
     */
    private $orderConditions = [];

    /**
     * List of fields for filters.
     *
     * @var array
     */
    private $whereConditions = [];

    /**
     * Filter entity result.
     *
     * @var array
     */
    private $select = [];

    /**
     * The ID of the entry to look for.
     *
     * @var string|null
     */
    private $linksToEntry;

    /**
     * The ID of the asset to look for.
     *
     * @var string|null
     */
    private $linksToAsset;

    /**
     * Query constructor.
     */
    public function __construct()
    {
    }

    /**
     * Returns the parameters to execute this query.
     *
     * @return array
     */
    public function getQueryData()
    {
        return \array_merge($this->whereConditions, [
            'limit' => $this->limit,
            'skip' => $this->skip,
            'content_type' => $this->contentType,
            'mimetype_group' => $this->mimeTypeGroup,
            'order' => $this->orderConditions ? \implode(',', $this->orderConditions) : \null,
            'select' => $this->select,
            'links_to_entry' => $this->linksToEntry,
            'links_to_asset' => $this->linksToAsset,
        ]);
    }

    /**
     * The urlencoded query string for this query.
     *
     * @return string
     */
    public function getQueryString()
    {
        return \http_build_query($this->getQueryData(), '', '&', \PHP_QUERY_RFC3986);
    }

    /**
     * Sets the index of the first result to retrieve. To reset set to NULL.
     *
     * @param int|null $skip The index of the first result that will be retrieved. Must be >= 0.
     *
     * @throws \RangeException If $skip is not within the specified range
     *
     * @return $this
     */
    public function setSkip($skip)
    {
        if (\null !== $skip && $skip < 0) {
            throw new \RangeException(\sprintf(
                'Skip value must be 0 or bigger, "%d" given.',
                $skip
            ));
        }

        $this->skip = $skip;

        return $this;
    }

    /**
     * Set the maximum number of results to retrieve. To reset set to NULL;.
     *
     * @param int|null $limit The maximum number of results to retrieve, must be between 1 and 1000 or null
     *
     * @throws \RangeException If $maxArguments is not withing the specified range
     *
     * @return $this
     */
    public function setLimit($limit)
    {
        if (\null !== $limit && ($limit < 1 || $limit > 1000)) {
            throw new \RangeException(\sprintf(
                'Limit value must be between 0 and 1000, "%d" given.',
                $limit
            ));
        }

        $this->limit = $limit;

        return $this;
    }

    /**
     * Set the order of the items retrieved by this query.
     *
     * Note that when ordering Entries by fields you must set the content_type URI query parameter to the ID of
     * the Content Type you want to filter by. Can be called multiple times to order by multiple values.
     *
     * @param string|null $field
     * @param bool        $reverse
     *
     * @return $this
     */
    public function orderBy($field, $reverse = \false)
    {
        if ($reverse) {
            $field = '-'.$field;
        }

        $this->orderConditions[] = $field;

        return $this;
    }

    /**
     * Set the content type to which results should be limited. Set to NULL to not filter for a content type.
     *
     * Only works when querying entries.
     *
     * @param string|null $contentType
     *
     * @return $this
     */
    public function setContentType($contentType)
    {
        $this->contentType = $contentType;

        return $this;
    }

    /**
     * @param string|null $group
     *
     * @throws \InvalidArgumentException if $group is not a valid value
     *
     * @return $this
     */
    public function setMimeTypeGroup($group)
    {
        $validGroups = [
            'attachment',
            'plaintext',
            'image',
            'audio',
            'video',
            'richtext',
            'presentation',
            'spreadsheet',
            'pdfdocument',
            'archive',
            'code',
            'markup',
        ];
        if (\null !== $group && !\in_array($group, $validGroups, \true)) {
            throw new \InvalidArgumentException(\sprintf(
                'Unknown MIME-type group "%s" given. Expected "%s" or null.',
                $group,
                \implode(', ', $validGroups)
            ));
        }

        $this->mimeTypeGroup = $group;

        return $this;
    }

    /**
     * Add a filter condition to this query.
     *
     * Valid operators are
     * - ne
     * - all
     * - in
     * - nin
     * - exists
     * - lt
     * - lte
     * - gt
     * - gte
     * - match
     * - near
     * - within
     *
     * @param string                                   $field
     * @param string|array|\DateTimeInterface|Location $value
     * @param string|null                              $operator the operator to use for this condition.
     *                                                           Default is strict equality
     *
     * @throws \InvalidArgumentException If $operator is not one of the valid values
     *
     * @return $this
     */
    public function where($field, $value, $operator = \null)
    {
        $validOperators = [
            'ne', // Not equal
            'all', // Multiple values
            'in', // Includes
            'nin', // Excludes
            'exists', // Exists
            'lt', // Less than
            'lte', // Less than or equal to
            'gt', // Greater than
            'gte', // Greater than or equal to,
            'match', // Full text search
            'near', // Nearby (for locations)
            'within', // Within an rectangle (for locations)
        ];
        if (\null !== $operator && !\in_array($operator, $validOperators, \true)) {
            throw new \InvalidArgumentException(\sprintf(
                'Unknown operator "%s" given. Expected "%s" or null.',
                $operator,
                \implode(', ', $validOperators)
            ));
        }

        if ($value instanceof \DateTimeInterface) {
            $value = $value->format(self::DATE_FORMAT);
        }
        if ($value instanceof Location) {
            $value = $value->queryStringFormatted();
        }
        if (\is_array($value)) {
            $value = \implode(',', $value);
        }

        $urlParameter = $operator
            ? $field.'['.$operator.']'
            : $field;

        $this->whereConditions[$urlParameter] = $value;

        return $this;
    }

    /**
     * The select operator allows you to choose what to return from an entity.
     * You provide one or multiple JSON paths and the API will return the properties at those paths.
     *
     * To only request the metadata simply query for 'sys'.
     *
     * @param array $select
     *
     * @return $this
     */
    public function select(array $select)
    {
        $parts = ['sys'];
        foreach ($select as $part) {
            if ('sys' === $part || 0 === \mb_strpos($part, 'sys.')) {
                continue;
            }
            $parts[] = $part;
        }

        $this->select = \implode(',', $parts);

        return $this;
    }

    /**
     * Filters for all entries that link to an entry.
     *
     * @param string $entryId
     *
     * @return $this
     */
    public function linksToEntry($entryId)
    {
        $this->linksToEntry = $entryId;

        return $this;
    }

    /**
     * Filters for all entries that link to an asset.
     *
     * @param string $assetId
     *
     * @return $this
     */
    public function linksToAsset($assetId)
    {
        $this->linksToAsset = $assetId;

        return $this;
    }
}
