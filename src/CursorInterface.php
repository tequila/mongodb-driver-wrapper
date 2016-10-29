<?php

namespace Tequila\MongoDB;

interface CursorInterface extends \Iterator
{
    /**
     * @return \MongoDB\Driver\CursorId
     */
    public function getId();

    /**
     * @return \MongoDB\Driver\Server
     */
    public function getServer();

    /**
     * @return bool
     */
    public function isDead();

    /**
     * @param array $typeMap
     * @void
     */
    public function setTypeMap(array $typeMap);

    /**
     * @return array
     */
    public function toArray();
}