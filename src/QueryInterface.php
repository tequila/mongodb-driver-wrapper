<?php

namespace Tequila\MongoDB;

interface QueryInterface extends OptionsProviderInterface
{
    /**
     * @return array|object
     */
    public function getFilter();
}