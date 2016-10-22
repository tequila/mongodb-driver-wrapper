<?php

namespace Tequila\MongoDB;

interface CommandInterface extends OptionsAwareInterface
{
    /**
     * @return bool Whether this command must be executed on a primary server
     */
    public function needsPrimaryServer();
}