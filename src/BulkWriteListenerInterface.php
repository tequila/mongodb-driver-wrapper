<?php

namespace Tequila\MongoDB;

interface BulkWriteListenerInterface
{
    /**
     * @param array|object $filter
     * @param array $options
     */
    public function beforeDelete($filter, array $options = []);

    /**
     * @param array|object $document
     */
    public function beforeInsert($document);

    /**
     * @param array|object $filter
     * @param array|object $update
     * @param array $options
     */
    public function beforeUpdate($filter, $update, array $options = []);
}