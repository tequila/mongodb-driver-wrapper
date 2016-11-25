<?php

namespace Tequila\MongoDB;

class SimpleCommand implements CommandInterface
{
    /**
     * @var object options to pass to @see \MongoDB\Driver\Command::__construct()
     */
    private $options;

    /**
     * @param array|object $options
     */
    public function __construct($options)
    {
        $this->options = (object)$options;
    }

    /**
     * @inheritdoc
     */
    public function getOptions(Server $server)
    {
        return $this->options;
    }
}