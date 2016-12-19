<?php

namespace Tequila\MongoDB;

interface QueryInterface extends OptionsProviderInterface
{
    const CURSOR_NON_TAILABLE = 1;
    const CURSOR_TAILABLE = 2;
    const CURSOR_TAILABLE_AWAIT = 3;

    /**
     * @return array|object
     */
    public function getFilter();
}