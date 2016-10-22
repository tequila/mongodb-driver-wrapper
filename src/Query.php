<?php

namespace Tequila\MongoDB;

use Tequila\MongoDB\Exception\UnsupportedException;

class Query implements OptionsAwareInterface
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
        $wireVersionForCollationOption = 5;

        if (isset($this->options['collation']) && $serverInfo->supportsFeature($wireVersionForCollationOption)) {
            throw new UnsupportedException('Option "collation" is not supported by the server');
        }

        return $this->options;
    }
}