<?php

namespace Tequila\MongoDB;

use MongoDB\Driver\ReadPreference;
use MongoDB\Driver\WriteConcern;

interface ManagerInterface
{
    /**
     * @param string $namespace
     * @param BulkProviderInterface $bulkWrite
     * @param WriteConcern $writeConcern
     * @return WriteResult
     */
    public function executeBulkWrite($namespace, BulkProviderInterface $bulkWrite, WriteConcern $writeConcern);

    /**
     * @param string $databaseName
     * @param CommandInterface $command
     * @param ReadPreference $readPreference
     * @return \MongoDB\Driver\Cursor
     */
    public function executeCommand($databaseName, CommandInterface $command, ReadPreference $readPreference);

    /**
     * @param $namespace
     * @param QueryInterface $query
     * @param ReadPreference $readPreference
     * @return \MongoDB\Driver\Cursor
     */
    public function executeQuery($namespace, QueryInterface $query, ReadPreference $readPreference);

    /**
     * @return ReadPreference
     */
    public function getReadPreference();

    /**
     * @return WriteConcern
     */
    public function getWriteConcern();

    /**
     * @return \MongoDB\Driver\Server[]
     */
    public function getServers();

    /**
     * @param ReadPreference $readPreference
     * @return \MongoDB\Driver\Server
     */
    public function selectServer(ReadPreference $readPreference = null);
}