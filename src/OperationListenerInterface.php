<?php

namespace Tequila\MongoDB;


interface OperationListenerInterface
{
    public function beforeBulkWrite(Server $server, $namespace, BulkWrite $bulkWrite);

    public function afterBulkWrite(Server $server, $namespace, BulkWrite $bulkWrite);

    public function beforeCommand(Server $server, $databaseName, $commandOptions);

    public function afterCommand(Server $server, $databaseName, $commandOptions, CursorInterface $cursor);

    public function beforeQuery(Server $server, $namespace, $filter, $options);

    public function afterQuery(Server $server, $namespace, $filter, $options, CursorInterface $cursor);
}