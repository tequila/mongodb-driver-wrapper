<?php

namespace Tequila\MongoDB;

class SimpleQuery implements QueryInterface
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
     * @inheritdoc
     */
    public function getOptions(Server $server)
    {
        return $this->options;
    }
}