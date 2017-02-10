<?php

namespace Tequila\MongoDB;

use MongoDB\Driver\ReadPreference;
use MongoDB\Driver\WriteConcern;

/**
 * This class wraps \MongoDB\Driver\Manager instance.
 * You can extend this class to have ability to intercept the calls to the driver Manager methods,
 * for example, in order to dispatch events.
 * Also this class can be used to create mocks, when you need to test, what methods of the Manager will be called
 */
class Manager
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
     * @var OperationListenerInterface
     */
    private $operationListener;

    /**
     * Wraps @see \MongoDB\Driver\Manager::__construct()
     *
     * @param string $uri
     * @param array $uriOptions
     * @param array $driverOptions
     */
    public function __construct($uri = 'mongodb://127.0.0.1/', array $uriOptions = [], array $driverOptions = [])
    {
        // Sorting options forces two \MongoDB\Driver\Manager with the same options to be "equal".
        // This allows to reuse cached libmongoc connections
        ksort($uriOptions);
        ksort($driverOptions);

        $this->uri = $uri;
        $this->uriOptions = $uriOptions;
        $this->driverOptions = $driverOptions;
        $this->wrappedManager = new \MongoDB\Driver\Manager(
            $this->uri,
            $this->uriOptions,
            $this->driverOptions
        );
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
    public function executeBulkWrite($namespace, BulkWrite $bulkWrite, WriteConcern $writeConcern = null)
    {
        $server = $this->selectServer(new ReadPreference(ReadPreference::RP_PRIMARY));
        if (null !== $this->operationListener) {
            $this->operationListener->beforeBulkWrite($server, $namespace, $bulkWrite);
        }
        $writeResult = $server->executeBulkWrite($namespace, $bulkWrite->compile($server), $writeConcern);
        if (null !== $this->operationListener) {
            $this->operationListener->afterBulkWrite($server, $namespace, $bulkWrite);
        }

        $wrappedResult = new WriteResult($writeResult, $bulkWrite->getInsertedIds());

        return $wrappedResult;
    }

    /**
     * @inheritdoc
     */
    public function executeCommand($databaseName, CommandInterface $command, ReadPreference $readPreference = null)
    {
        $readPreference = $readPreference ?: $this->getReadPreference();
        $server = $this->selectServer($readPreference);
        $commandOptions = $command->getOptions($server);

        if (null !== $this->operationListener) {
            $this->operationListener->beforeCommand($server, $databaseName, $commandOptions);
        }

        $driverCommand = new \MongoDB\Driver\Command($commandOptions);

        $cursor = $server->executeCommand(
            $databaseName,
            $driverCommand
        );

        $cursor = new Cursor($cursor);

        if (null !== $this->operationListener) {
            $this->operationListener->afterCommand($server, $databaseName, $commandOptions, $cursor);
        }

        return $cursor;
    }

    /**
     * @inheritdoc
     */
    public function executeQuery($namespace, QueryInterface $query, ReadPreference $readPreference = null)
    {
        $readPreference = $readPreference ?: $this->getReadPreference();

        $server = $this->selectServer($readPreference);
        $queryOptions = $query->getOptions($server);

        if (null !== $this->operationListener) {
            $this->operationListener->beforeQuery($server, $namespace, $query->getFilter(), $queryOptions);
        }

        $driverQuery = new \MongoDB\Driver\Query(
            $query->getFilter(),
            $queryOptions
        );

        $cursor = $server->executeQuery($namespace, $driverQuery);

        $cursor = new Cursor($cursor);

        if (null !== $this->operationListener) {
            $this->operationListener->afterQuery($server, $namespace, $query->getFilter(), $queryOptions, $cursor);
        }

        return $cursor;
    }

    /**
     * @inheritdoc
     */
    public function getReadConcern()
    {
        return $this->wrappedManager->getReadConcern();
    }

    /**
     * @inheritdoc
     */
    public function getReadPreference()
    {
        return $this->wrappedManager->getReadPreference();
    }

    /**
     * @inheritdoc
     */
    public function getWriteConcern()
    {
        return $this->wrappedManager->getWriteConcern();
    }

    /**
     * @inheritdoc
     */
    public function getServers()
    {
        /** @var array $servers */
        $servers = $this->wrappedManager->getServers();

        return array_map(function(\MongoDB\Driver\Server $server) {
            return new Server($server);
        }, $servers);
    }

    /**
     * @inheritdoc
     */
    public function selectServer(ReadPreference $readPreference)
    {
        $server = $this->wrappedManager->selectServer($readPreference);

        return new Server($server);
    }

    /**
     * @param OperationListenerInterface $listener
     */
    public function setOperationListener(OperationListenerInterface $listener)
    {
        $this->operationListener = $listener;
    }
}