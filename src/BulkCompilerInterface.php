<?php

namespace Tequila\MongoDB;

interface BulkCompilerInterface
{
    /**
     * @param Server $server
     * @return array
     */
    public function getOptions(Server $server);

    /**
     * @param BulkWrite $bulkWrite
     * @param Server $server
     * @return \MongoDB\Driver\BulkWrite
     */
    public function processBulk(BulkWrite $bulkWrite, Server $server);
}