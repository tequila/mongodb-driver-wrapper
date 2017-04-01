<?php

namespace Tequila\MongoDB;

interface QueryListenerInterface
{
    /**
     * @param string $namespace
     * @param array|object $filter
     * @param array $options
     * @param QueryCursor $cursor
     */
    public function onQueryExecuted($namespace, $filter, array $options, QueryCursor $cursor);
}