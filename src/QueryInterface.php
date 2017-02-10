<?php

namespace Tequila\MongoDB;

interface QueryInterface
{
    const CURSOR_NON_TAILABLE = 1;
    const CURSOR_TAILABLE = 2;
    const CURSOR_TAILABLE_AWAIT = 3;

    /**
     * @return array|object
     */
    public function getFilter();

    /**
     * @param Server $server is passed to resolve query options depending on server version
     * @return array|object
     */
    public function getOptions(Server $server);
}