<?php

namespace Tequila\MongoDB;

interface CommandInterface
{
    /**
     * @param Server $server is passed to resolve command options depending on server version
     * @return array|object
     */
    public function getOptions(Server $server);
}