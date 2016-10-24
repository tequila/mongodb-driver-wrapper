<?php

namespace Tequila\MongoDB;

interface QueryInterface extends OptionsProviderInterface
{
    /**
     * @return array|object
     */
    public function getFilter();

    /**
     * @inheritdoc
     */
    public function getOptions(ServerInfo $serverInfo);
}