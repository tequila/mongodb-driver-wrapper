<?php

namespace Tequila\MongoDB;

use MongoDB\Driver\Server;

interface CommandInterface
{
    /**
     * @param Server $server is passed for command to resolve it's options depending on server version
     * @return array
     */
    public function getOptions(Server $server = null);

    /**
     * @return bool Whether this command must be executed on a primary server
     */
    public function needsPrimaryServer();
}