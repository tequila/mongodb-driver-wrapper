<?php

namespace Tequila\MongoDB;

use MongoDB\Driver\BulkWrite;
use MongoDB\Driver\Command;
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
        return $this->wrappedServer->executeCommand($databaseName, $command);
    }

    /**
     * @param string $namespace
     * @param \MongoDB\Driver\Query $query
     * @param ReadPreference|null $readPreference
     * @return \MongoDB\Driver\Cursor
     */
    public function executeQuery($namespace, \MongoDB\Driver\Query $query, ReadPreference $readPreference = null)
    {
        return $this->wrappedServer->executeQuery($namespace, $query);
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
        return $this->wrappedServer->isSecondary();
    }

    /**
     * @param int $wireVersion
     * @return bool
     */
    public function supportsWireVersion($wireVersion)
    {
        $info = $this->getInfo();
        $wireVersion = (int)$wireVersion;
        $minWireVersion = array_key_exists('minWireVersion', $info) ? $info['minWireVersion'] : 0;
        $maxWireVersion = array_key_exists('maxWireVersion', $info) ? $info['maxWireVersion'] : 0;

        return $wireVersion >= $minWireVersion && $wireVersion <= $maxWireVersion;
    }

    /**
     * @return bool
     */
    public function supportsCollation()
    {
        return $this->supportsWireVersion(5);
    }

    /**
     * @return bool
     */
    public function supportsDocumentValidation()
    {
        return $this->supportsWireVersion(4);
    }

    /**
     * @return bool
     */
    public function supportsReadConcern()
    {
        return $this->supportsWireVersion(4);
    }

    /**
     * @return bool
     */
    public function supportsWriteConcern()
    {
        return $this->supportsWireVersion(5);
    }
}