<?php

namespace Tequila\MongoDB;

class Command implements CommandInterface
{
    /**
     * @var object options to pass to @see \MongoDB\Driver\Command::__construct()
     */
    private $options;

    /**
     * @var bool
     */
    private $needsPrimaryServer;

    /**
     * @param array|object $options
     * @param bool $needsPrimaryServer Whether this command needs to be executed on a primary server or not
     */
    public function __construct($options, $needsPrimaryServer)
    {
        $this->options = (object)$options;
        $this->needsPrimaryServer = (bool)$needsPrimaryServer;
    }

    /**
     * @inheritdoc
     */
    public function getOptions(Server $server)
    {
        return $this->options;
    }

    /**
     * @inheritdoc
     */
    public function needsPrimaryServer()
    {
        return $this->needsPrimaryServer();
    }
}