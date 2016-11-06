<?php

namespace Tequila\MongoDB;

interface OptionsProviderInterface
{
    /**
     * @param Server $server is passed for instance to resolve it's options depending on server version
     * @return array|object
     */
    public function getOptions(Server $server);
}