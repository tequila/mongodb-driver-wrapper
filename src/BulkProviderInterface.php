<?php

namespace Tequila\MongoDB;

use MongoDB\BSON\ObjectID;

interface BulkProviderInterface
{
    /**
     * @return \MongoDB\Driver\BulkWrite
     */
    public function getBulk();

    /**
     * @return array|ObjectID[] inserted ids
     */
    public function getInsertedIds();
}