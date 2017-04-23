<?php

namespace Tequila\MongoDB;

use MongoDB\BSON\ObjectID;
use MongoDB\Driver\WriteConcernError;
use MongoDB\Driver\WriteError;
use Tequila\MongoDB\Exception\BadMethodCallException;

/**
 * This class wraps @see \MongoDB\Driver\WriteResult to provide inserted ids. @see getInsertedIds()
 */
class WriteResult
{
    use Traits\ExecutionTimeTrait;

    /**
     * @var WriteResult
     */
    private $writeResult;

    /**
     * @var array
     */
    private $insertedIds;

    /**
     * @var bool
     */
    private $acknowledged;

    /**
     * @param \MongoDB\Driver\WriteResult $writeResult
     * @param array|ObjectID[] $insertedIds
     */
    public function __construct(\MongoDB\Driver\WriteResult $writeResult, array $insertedIds = [])
    {
        $this->writeResult = $writeResult;
        $this->insertedIds = $insertedIds;
        $this->acknowledged = $writeResult->isAcknowledged();
    }

    /**
     * @return array
     */
    public function __debugInfo()
    {
        return [
            'insertedIds' => $this->insertedIds,
            'isAcknowledged' => $this->acknowledged,
            'wrappedWriteResult' => $this->writeResult,
        ];
    }

    /**
     * @return array
     */
    public function getInsertedIds()
    {
        return $this->insertedIds;
    }

    /**
     * @return int
     */
    public function getDeletedCount()
    {
        $this->ensureAcknowledgedWriteResult(__METHOD__);

        return $this->writeResult->getDeletedCount();
    }

    /**
     * @return int
     */
    public function getInsertedCount()
    {
        $this->ensureAcknowledgedWriteResult(__METHOD__);

        return $this->writeResult->getInsertedCount();
    }

    /**
     * @return int
     */
    public function getMatchedCount()
    {
        $this->ensureAcknowledgedWriteResult(__METHOD__);

        return $this->writeResult->getMatchedCount();
    }

    /**
     * @return int
     */
    public function getModifiedCount()
    {
        $this->ensureAcknowledgedWriteResult(__METHOD__);

        return $this->writeResult->getModifiedCount();
    }

    /**
     * @return int
     */
    public function getUpsertedCount()
    {
        $this->ensureAcknowledgedWriteResult(__METHOD__);

        return $this->writeResult->getUpsertedCount();
    }

    /**
     * @return \MongoDB\BSON\ObjectID[]
     */
    public function getUpsertedIds()
    {
        $this->ensureAcknowledgedWriteResult(__METHOD__);

        return $this->writeResult->getUpsertedIds();
    }

    /**
     * @return WriteConcernError|null
     */
    public function getWriteConcernError()
    {
        $this->ensureAcknowledgedWriteResult(__METHOD__);

        return $this->writeResult->getWriteConcernError();
    }

    /**
     * @return WriteError[]
     */
    public function getWriteErrors()
    {
        $this->ensureAcknowledgedWriteResult(__METHOD__);

        return $this->writeResult->getWriteErrors();
    }

    /**
     * @return \MongoDB\Driver\Server
     */
    public function getServer()
    {
        return $this->writeResult->getServer();
    }

    /**
     * @return bool
     */
    public function isAcknowledged()
    {
        return $this->acknowledged;
    }

    private function ensureAcknowledgedWriteResult($method)
    {
        if (!$this->acknowledged) {
            throw new BadMethodCallException(
                sprintf(
                    'Method %s cannot be called on unacknowledged write result.',
                    $method
                )
            );
        }
    }
}
