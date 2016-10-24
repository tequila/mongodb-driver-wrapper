<?php

namespace Tequila\MongoDB;

interface CommandInterface extends OptionsProviderInterface
{
    /**
     * @return bool Whether this command must be executed on a primary server
     */
    public function needsPrimaryServer();
}