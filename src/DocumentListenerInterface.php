<?php

namespace Tequila\MongoDB;

interface DocumentListenerInterface
{
    /**
     * @param QueryCursor $cursor
     * @param array|object|\MongoDB\BSON\Unserializable $document
     */
    public function onDocument(QueryCursor $cursor, $document);
}