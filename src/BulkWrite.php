<?php

namespace Tequila\MongoDB;

use MongoDB\BSON\ObjectID;
use MongoDB\BSON\Serializable;

class BulkWrite implements BulkProviderInterface
{
    /**
     * @var int
     */
    private $count = 0;

    /**
     * @var array
     */
    private $insertedIds = [];

    /**
     * @var \MongoDB\Driver\BulkWrite
     */
    private $wrappedBulk;

    /**
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->wrappedBulk = new \MongoDB\Driver\BulkWrite($options);
    }

    /**
     * @inheritdoc
     */
    public function getBulk()
    {
        return $this->wrappedBulk;
    }

    /**
     * @inheritdoc
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
        $id = $this->wrappedBulk->insert($document);
        if (null === $id) {
            $id = $this->extractIdFromDocument($document);
        }

        $this->insertedIds[$this->count] = $id;
        $this->count +=1 ;

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
        $this->wrappedBulk->update($filter, $update, $options);
        $this->count += 1;
    }

    /**
     * Wraps @see \MongoDB\Driver\BulkWrite::delete()
     *
     * @param array|object $filter
     * @param array $options
     */
    public function delete($filter, array $options = [])
    {
        $this->wrappedBulk->delete($filter, $options);
        $this->count += 1;
    }

    /**
     * @return int
     */
    public function count()
    {
        return $this->count;
    }

    /**
     * @param array|object $document
     * @return ObjectID|mixed
     */
    private function extractIdFromDocument($document)
    {
        if ($document instanceof Serializable) {
            return self::extractIdFromDocument($document->bsonSerialize());
        }

        return is_array($document) ? $document['_id'] : $document->_id;
    }
}