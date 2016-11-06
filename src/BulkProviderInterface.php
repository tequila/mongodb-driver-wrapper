<?php

namespace Tequila\MongoDB;

use MongoDB\BSON\ObjectID;

interface BulkProviderInterface
{
    /**
     * @param ServerInfo $serverInfo
     * @return \MongoDB\Driver\BulkWrite
     */
    public function getBulk(ServerInfo $serverInfo);

    /**
     * @return array|ObjectID[] inserted ids
     */
    public function getInsertedIds();
}