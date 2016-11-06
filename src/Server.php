<?php

namespace Tequila\MongoDB;

use MongoDB\Driver\BulkWrite;
use MongoDB\Driver\Command;
use MongoDB\Driver\Query;
use MongoDB\Driver\ReadPreference;
use MongoDB\Driver\WriteConcern;

class Server
{
    /**
     * @var \MongoDB\Driver\Server
     */
    private $wrappedServer;

    /**
     * @param \MongoDB\Driver\Server $server
     */
    public function __construct(\MongoDB\Driver\Server $server)
    {
        $this->wrappedServer = $server;
    }

    public function executeBulkWrite(
        $namespace,
        BulkWrite $bulkWrite,
        WriteConcern $writeConcern = null
    ) {
        return $this->wrappedServer->executeBulkWrite(
            $namespace,
            $bulkWrite,
            $writeConcern
        );
    }

    /**
     * @param string $databaseName
     * @param Command $command
     * @param ReadPreference|null $readPreference
     * @return \MongoDB\Driver\Cursor
     */
    public function executeCommand($databaseName, Command $command, ReadPreference $readPreference = null)
    {
        return $this->wrappedServer->executeCommand($databaseName, $command, $readPreference);
    }

    /**
     * @param string $namespace
     * @param Query $query
     * @param ReadPreference|null $readPreference
     * @return \MongoDB\Driver\Cursor
     */
    public function executeQuery($namespace, Query $query, ReadPreference $readPreference = null)
    {
        return $this->wrappedServer->executeQuery($namespace, $query, $readPreference);
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->wrappedServer->getHost();
    }

    /**
     * @return array
     */
    public function getInfo()
    {
        return $this->wrappedServer->getInfo();
    }

    /**
     * @return int
     */
    public function getLatency()
    {
        return $this->wrappedServer->getLatency();
    }

    /**
     * @return int
     */
    public function getPort()
    {
        return $this->wrappedServer->getPort();
    }

    /**
     * @return array
     */
    public function getTags()
    {
        return $this->wrappedServer->getTags();
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->wrappedServer->getType();
    }

    /**
     * @return bool
     */
    public function isArbiter()
    {
        return $this->wrappedServer->isArbiter();
    }

    /**
     * @return bool
     */
    public function isHidden()
    {
        return $this->wrappedServer->isHidden();
    }

    /**
     * @return bool
     */
    public function isPassive()
    {
        return $this->wrappedServer->isPassive();
    }

    /**
     * @return bool
     */
    public function isPrimary()
    {
        return $this->wrappedServer->isPrimary();
    }

    /**
     * @return bool
     */
    public function isSecondary()
    {
        return $this->wrappedServer->isArbiter();
    }

    /**
     * @param int $featureWireVersion
     * @return bool
     */
    public function supportsFeature($featureWireVersion)
    {
        $info = $this->getInfo();
        $featureWireVersion = (int)$featureWireVersion;
        $minWireVersion = array_key_exists('minWireVersion', $info) ? $info['minWireVersion'] : 0;
        $maxWireVersion = array_key_exists('maxWireVersion', $info) ? $info['maxWireVersion'] : 0;

        return $featureWireVersion >= $minWireVersion && $featureWireVersion <= $maxWireVersion;
    }

    /**
     * @return bool
     */
    public function supportsCollation()
    {
        return $this->supportsFeature(5);
    }

    /**
     * @return bool
     */
    public function supportsDocumentValidation()
    {
        return $this->supportsFeature(4);
    }

    /**
     * @return bool
     */
    public function supportsReadConcern()
    {
        return $this->supportsFeature(4);
    }

    /**
     * @return bool
     */
    public function supportsWriteConcern()
    {
        return $this->supportsFeature(5);
    }
}