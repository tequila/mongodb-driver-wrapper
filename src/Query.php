<?php

namespace Tequila\MongoDB;

class Query implements QueryInterface
{
    /**
     * @var object
     */
    private $filter;

    /**
     * @var array
     */
    private $options;

    /**
     * @param array|object $filter
     * @param array $options
     */
    public function __construct($filter, array $options = [])
    {
        $this->filter = (object)$filter;
        $this->options = $options;
    }

    public function __debugInfo()
    {
        return [
            'filter' => $this->filter,
            'queryOptions' => $this->options,
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
     * @param ServerInfo $serverInfo
     * @return array
     */
    public function getOptions(ServerInfo $serverInfo)
    {
        return $this->options;
    }
}