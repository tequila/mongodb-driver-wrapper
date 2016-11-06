<?php

namespace Tequila\MongoDB;

use MongoDB\BSON\ObjectID;

interface BulkProviderInterface
{
    /**
     * @param Server $server
     * @return \MongoDB\Driver\BulkWrite
     */
    public function getBulk(Server $server);

    /**
     * @return array|ObjectID[] inserted ids
     */
    public function getInsertedIds();
}