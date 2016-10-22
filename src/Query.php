<?php

namespace Tequila\MongoDB;

class Query
{
    /**
     * @var object
     */
    private $filter;

    /**
     * @var array
     */
    private $queryOptions;

    /**
     * @param array|object $filter
     * @param array $queryOptions
     */
    public function __construct($filter, array $queryOptions = [])
    {
        $this->filter = (object)$filter;
        $this->queryOptions = $queryOptions;
    }

    public function __debugInfo()
    {
        return [
            'filter' => $this->filter,
            'queryOptions' => $this->queryOptions,
        ];
    }

    /**
     * @return object
     */
    public function getFilter()
    {
        return $this->filter;
    }

    /**
     * @return array
     */
    public function getQueryOptions()
    {
        return $this->queryOptions;
    }
}