<?php

namespace Tequila\MongoDB;

interface BulkWriteListenerInterface
{
    /**
     * @param array|object $filter
     * @param array $options
     */
    public function onDelete($filter, array $options = []);

    /**
     * @param array|object $document
     */
    public function onInsert($document);

    /**
     * @param array|object $filter
     * @param array|object $update
     * @param array $options
     */
    public function onUpdate($filter, $update, array $options = []);
}