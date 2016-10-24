<?php

namespace Tequila\MongoDB;

interface OptionsProviderInterface
{
    /**
     * @param ServerInfo $serverInfo is passed for instance to resolve it's options depending on server version
     * @return array
     */
    public function getOptions(ServerInfo $serverInfo);
}