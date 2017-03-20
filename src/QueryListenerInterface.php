<?php

namespace Tequila\MongoDB;

interface QueryListenerInterface
{
    /**
     * @param string $namespace
     * @param array|object $filter
     * @param array $options
     * @param Cursor $cursor
     */
    public function onQueryExecuted($namespace, $filter, array $options, Cursor $cursor);
}