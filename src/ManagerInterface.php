<?php

namespace Tequila\MongoDB;

use MongoDB\Driver\ReadConcern;
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
    public function executeBulkWrite($namespace, BulkProviderInterface $bulkWrite, WriteConcern $writeConcern = null);

    /**
     * @param string $databaseName
     * @param CommandInterface $command
     * @param ReadPreference $readPreference
     * @return CursorInterface
     */
    public function executeCommand($databaseName, CommandInterface $command, ReadPreference $readPreference = null);

    /**
     * @param $namespace
     * @param QueryInterface $query
     * @param ReadPreference $readPreference
     * @return CursorInterface
     */
    public function executeQuery($namespace, QueryInterface $query, ReadPreference $readPreference = null);

    /**
     * @return ReadConcern
     */
    public function getReadConcern();

    /**
     * @return ReadPreference
     */
    public function getReadPreference();

    /**
     * @return WriteConcern
     */
    public function getWriteConcern();

    /**
     * @return Server[]
     */
    public function getServers();

    /**
     * @param ReadPreference $readPreference
     * @return Server
     */
    public function selectServer(ReadPreference $readPreference);
}