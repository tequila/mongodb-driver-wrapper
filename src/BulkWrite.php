<?php

namespace Tequila\MongoDB;

use MongoDB\BSON\ObjectID;
use MongoDB\BSON\Serializable;
use Tequila\MongoDB\Exception\BadMethodCallException;
use Tequila\MongoDB\Exception\InvalidArgumentException;
use Tequila\MongoDB\Exception\UnsupportedException;

class BulkWrite
{
    /**
     * @var int position of the currently compiled write model
     */
    private $currentPosition = 0;

    /**
     * @var array
     */
    private $insertedIds = [];

    /**
     * @var array
     */
    private $options;

    /**
     * @var \MongoDB\Driver\BulkWrite
     */
    private $wrappedBulk;

    /**
     * @var Server
     */
    private $server;

    /**
     * @var array
     */
    private $writeModels;

    /**
     * @param array $writeModels
     * @param array $options
     */
    public function __construct(array $writeModels, array $options = [])
    {
        if (empty($writeModels)) {
            throw new InvalidArgumentException('$writeModels array cannot be empty.');
        }

        $this->writeModels = $writeModels;
        $this->options = $options;
    }

    /**
     * @param Server $server
     * @return \MongoDB\Driver\BulkWrite
     */
    public function compile(Server $server)
    {
        $this->server = $server;

        $expectedPosition = 0;
        foreach ($this->writeModels as $position => $writeModel) {
            if (!$expectedPosition === $position) {
                throw new InvalidArgumentException(
                    sprintf('$writeModels is not a list. Unexpected index "%s".', $position)
                );
            }

            ++$expectedPosition;

            if (!$writeModel instanceof WriteModelInterface) {
                throw new InvalidArgumentException(
                    sprintf(
                        'Each write model must be an instance of "%s", "%s" given in $writeModels[%d].',
                        WriteModelInterface::class,
                        is_object($writeModel) ? get_class($writeModel) : gettype($writeModel),
                        $position
                    )
                );
            }

            $writeModel->writeToBulk($this);
        }

        return $this->wrappedBulk;
    }

    public function __debugInfo()
    {
        return [
            'count' => $this->currentPosition,
            'insertedIds' => $this->insertedIds,
            'wrappedBulk' => $this->wrappedBulk,
        ];
    }

    /**
     * @return ObjectID[]|mixed[]
     */
    public function getInsertedIds()
    {
        return $this->insertedIds;
    }

    /**
     * Wraps @see \MongoDB\Driver\BulkWrite::insert() to save inserted id and always return it
     *
     * @param array|object $document
     * @return ObjectID|mixed id of the inserted document
     */
    public function insert($document)
    {
        $this->ensureAllowedMethodCall(__METHOD__);

        $id = $this->getWrappedBulk()->insert($document);
        if (null === $id) {
            $id = $this->extractIdFromDocument($document);
        }

        $this->insertedIds[$this->currentPosition] = $id;
        $this->currentPosition += 1 ;

        return $id;
    }

    /**
     * Wraps @see \MongoDB\Driver\BulkWrite::update()
     *
     * @param array|object $filter
     * @param array|object $update
     * @param array $options
     */
    public function update($filter, $update, array $options = [])
    {
        $this->ensureAllowedMethodCall(__METHOD__);

        if (isset($options['collation']) && !$this->server->supportsCollation()) {
            throw new UnsupportedException(
                'Option "collation" is not supported by the server.'
            );
        }

        $this->getWrappedBulk()->update($filter, $update, $options);
        $this->currentPosition += 1;
    }

    /**
     * Wraps @see \MongoDB\Driver\BulkWrite::delete()
     *
     * @param array|object $filter
     * @param array $options
     */
    public function delete($filter, array $options = [])
    {
        $this->ensureAllowedMethodCall(__METHOD__);

        if (isset($options['collation']) && !$this->server->supportsCollation()) {
            throw new UnsupportedException(
                'Option "collation" is not supported by the server.'
            );
        }

        $this->getWrappedBulk()->delete($filter, $options);
        $this->currentPosition += 1;
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->writeModels);
    }

    /**
     * @param array|object $document
     * @return ObjectID|mixed
     */
    private function extractIdFromDocument(&$document)
    {
        if ($document instanceof Serializable) {
            return self::extractIdFromDocument($document->bsonSerialize());
        }

        return is_array($document) ? $document['_id'] : $document->_id;
    }

    /**
     * @return \MongoDB\Driver\BulkWrite
     */
    private function getWrappedBulk()
    {
        if (null === $this->wrappedBulk) {
            if (isset($this->options['bypassDocumentValidation']) && !$this->server->supportsDocumentValidation()) {
                throw new UnsupportedException(
                    'Option "bypassDocumentValidation" is not supported by the server.'
                );
            }

            $this->wrappedBulk = new \MongoDB\Driver\BulkWrite($this->options);
        }

        return $this->wrappedBulk;
    }

    private function ensureAllowedMethodCall($method)
    {
        // If method called not in compilation stage
        if (null === $this->server) {
            throw new BadMethodCallException(
                sprintf('Method "%s" is internal and should not be called explicitly.', $method)
            );
        }
    }
}