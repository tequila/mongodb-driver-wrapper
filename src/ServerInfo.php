<?php

namespace Tequila\MongoDB;

use MongoDB\Driver\Server;

class ServerInfo
{
    /**
     * @var array
     */
    private $info;

    public function __construct(Server $server)
    {
        $this->info = $server->getInfo();
    }

    /**
     * @param string $param
     * @param $default
     * @return mixed
     */
    public function get($param, $default = null)
    {
        return array_key_exists($param, $this->info) ? $this->info[$param] : $default;
    }

    /**
     * @param string $param
     * @return bool
     */
    public function has($param)
    {
        return array_key_exists($param, $this->info);
    }

    /**
     * @return bool
     */
    public function isMaster()
    {
        return (bool)$this->get('ismaster');
    }

    /**
     * @return int
     */
    public function getMaxBsonObjectSize()
    {
        return $this->get('maxBsonObjectSize', 0);
    }

    /**
     * @return int
     */
    public function getMaxMessageSizeBytes()
    {
        return $this->get('maxMessageSizeBytes', 0);
    }

    /**
     * @return int
     */
    public function getMaxWriteBatchSize()
    {
        return $this->get('maxWriteBatchSize', 0);
    }

    /**
     * @return \MongoDB\BSON\UTCDateTime|null
     */
    public function getLocalTime()
    {
        return $this->get('localTime');
    }

    /**
     * @return int
     */
    public function getMinWireVersion()
    {
        return $this->get('minWireVersion', 0);
    }

    /**
     * @return int
     */
    public function getMaxWireVersion()
    {
        return $this->get('maxWireVersion', 0);
    }

    /**
     * Tells whether the server supports some feature or not by checking that server wire versions range
     * includes wire version of the feature
     *
     * @param int $featureWireVersion
     * @return bool
     */
    public function supportsFeature($featureWireVersion)
    {
        return $featureWireVersion >= $this->getMinWireVersion() && $featureWireVersion <= $this->getMaxWireVersion();
    }
}