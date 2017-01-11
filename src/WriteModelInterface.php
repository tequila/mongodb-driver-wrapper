<?php

namespace Tequila\MongoDB;

interface WriteModelInterface
{
    /**
     * @param BulkWrite $bulk
     * @return
     * @void
     */
    public function writeToBulk(BulkWrite $bulk);
}