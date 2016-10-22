<?php

namespace Tequila\MongoDB;

use MongoDB\BSON\ObjectID;
use MongoDB\Driver\BulkWrite;
use MongoDB\Driver\WriteConcern;

interface BulkWriteInterface
{
    /**
     * Returns the options, needed to construct driver's BulkWrite instance
     *
     * @return array
     */
    public function getOptions();

    /**
     * Fills @see BulkWrite instance and returns inserted ids
     *
     * @param BulkWrite $bulkWrite
     * @return array|ObjectID[] inserted ids
     */
    public function configureBulk(BulkWrite $bulkWrite);

    /**
     * @return WriteConcern|null
     */
    public function getWriteConcern();

    /**
     * Wraps @see \MongoDB\Driver\BulkWrite::count()
     *
     * @return int
     */
    public function count();

    /**
     * Wraps @see \MongoDB\Driver\BulkWrite::delete()
     *
     * @param array|object $filter
     * @param array $options
     */
    public function delete($filter, array $options = []);

    /**
     * Wraps @see \MongoDB\Driver\BulkWrite::update()
     *
     * @param array|object $document
     * @return ObjectID
     */
    public function insert($document);

    /**
     * Wraps @see \MongoDB\Driver\BulkWrite::update()
     *
     * @param array|object $filter
     * @param array|object $update
     * @param array $options
     */
    public function update($filter, $update, array $options = []);
}