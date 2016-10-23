<?php

namespace Tequila\MongoDB;

use MongoDB\Driver\Command;
use MongoDB\Driver\ReadPreference;
use MongoDB\Driver\WriteConcern;

/**
 * This class wraps \MongoDB\Driver\Manager instance
 * You can extend this class to have ability to intercept the calls to the driver Manager methods,
 * for example, in order dispatch events.
 * Also this class can be used to create mocks, when you need to test, what methods of the Manager will be called
 */
class Manager implements ManagerInterface
{
    /**
     * @var string
     */
    private $uri;

    /**
     * @var array
     */
    private $uriOptions;

    /**
     * @var array
     */
    private $driverOptions;

    /**
     * @var \MongoDB\Driver\Manager
     */
    private $wrappedManager;

    /**
     * Wraps @see \MongoDB\Driver\Manager::__construct()
     *
     * @param string $uri
     * @param array $uriOptions
     * @param array $driverOptions
     */
    public function __construct($uri = 'mongodb://127.0.0.1/', array $uriOptions = [], array $driverOptions = [])
    {
        $this->uri = $uri;
        $this->uriOptions = $uriOptions;
        $this->driverOptions = $driverOptions;
    }

    /**
     * Returns properties for debugging purpose
     *
     * @return array
     */
    public function __debugInfo()
    {
        return [
            'uri' => $this->uri,
            'uriOptions' => $this->uriOptions,
            'driverOptions' => $this->driverOptions,
            'wrappedManager' => $this->wrappedManager,
        ];
    }

    /**
     * @inheritdoc
     */
    public function executeBulkWrite($namespace, BulkProviderInterface $bulkProvider, WriteConcern $writeConcern)
    {
        $writeResult = $this->getWrappedManager()->executeBulkWrite(
            $namespace,
            $bulkProvider->getBulk(),
            $writeConcern
        );

        $wrappedResult = new WriteResult($writeResult, $bulkProvider->getInsertedIds());

        return $wrappedResult;
    }

    /**
     * @inheritdoc
     */
    public function executeCommand($databaseName, CommandInterface $command, ReadPreference $readPreference)
    {
        if ($command->needsPrimaryServer() && ReadPreference::RP_PRIMARY !== $readPreference->getMode()) {
            $readPreference = new ReadPreference(ReadPreference::RP_PRIMARY);
        }

        $server = $this->selectServer($readPreference);
        $serverInfo = new ServerInfo($server);

        $driverCommand = new Command($command->getOptions($serverInfo));

        return $server->executeCommand(
            $databaseName,
            $driverCommand
        );
    }

    /**
     * @inheritdoc
     */
    public function executeQuery($namespace, Query $query, ReadPreference $readPreference)
    {
        $server = $this->getWrappedManager()->selectServer($readPreference);
        $driverQuery = new \MongoDB\Driver\Query($query->getFilter(), $query->getOptions());

        return $server->executeQuery($namespace, $driverQuery);
    }

    /**
     * @inheritdoc
     */
    public function getReadPreference()
    {
        return $this->getWrappedManager()->getReadPreference();
    }

    /**
     * @inheritdoc
     */
    public function getWriteConcern()
    {
        return $this->getWrappedManager()->getWriteConcern();
    }

    /**
     * @inheritdoc
     */
    public function getServers()
    {
        return $this->getWrappedManager()->getServers();
    }

    /**
     * @inheritdoc
     */
    public function selectServer(ReadPreference $readPreference = null)
    {
        return $this->getWrappedManager()->selectServer($readPreference);
    }

    /**
     * @return \MongoDB\Driver\Manager
     */
    private function getWrappedManager()
    {
        if (null === $this->wrappedManager) {
            $this->wrappedManager = new \MongoDB\Driver\Manager(
                $this->uri,
                $this->uriOptions,
                $this->driverOptions
            );
        }

        return $this->wrappedManager;
    }
}