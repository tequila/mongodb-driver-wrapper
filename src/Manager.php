<?php

namespace Tequila\MongoDB;

use MongoDB\Driver\Command;
use MongoDB\Driver\ReadPreference;
use MongoDB\Driver\WriteConcern;

/**
 * This class wraps \MongoDB\Driver\Manager instance.
 * You can extend this class to have ability to intercept the calls to the driver Manager methods,
 * for example, in order to dispatch events.
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
        // Sorting options forces wrapped \MongoDB\Driver\Manager to reuse cached libmongoc connections
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
    public function executeBulkWrite($namespace, BulkProviderInterface $bulkProvider, WriteConcern $writeConcern = null)
    {
        $writeConcern = $writeConcern ?: $this->getWriteConcern();
        $server = $this->selectServer(new ReadPreference(ReadPreference::RP_PRIMARY));
        $bulk = $bulkProvider->getBulk($server);
        $writeResult = $server->executeBulkWrite($namespace, $bulk, $writeConcern);
        $wrappedResult = new WriteResult($writeResult, $bulkProvider->getInsertedIds());

        return $wrappedResult;
    }

    /**
     * @inheritdoc
     */
    public function executeCommand($databaseName, CommandInterface $command, ReadPreference $readPreference = null)
    {
        $readPreference = $readPreference ?: $this->getReadPreference();
        if ($command->needsPrimaryServer()) {
            $readPreference = new ReadPreference(ReadPreference::RP_PRIMARY);
        }

        $server = $this->selectServer($readPreference);
        $driverCommand = new Command($command->getOptions($server));

        $cursor = $server->executeCommand(
            $databaseName,
            $driverCommand
        );

        return new Cursor($cursor);
    }

    /**
     * @inheritdoc
     */
    public function executeQuery($namespace, QueryInterface $query, ReadPreference $readPreference = null)
    {
        $readPreference = $readPreference ?: $this->getReadPreference();

        $server = $this->selectServer($readPreference);
        $driverQuery = new \MongoDB\Driver\Query(
            $query->getFilter(),
            $query->getOptions($server)
        );

        $cursor = $server->executeQuery($namespace, $driverQuery);

        return new Cursor($cursor);
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
}