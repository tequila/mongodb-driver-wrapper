<?php

namespace Tequila\MongoDB;

interface DocumentListenerInterface
{
    /**
     * @param Cursor $cursor
     * @param array|object|\MongoDB\BSON\Unserializable $document
     */
    public function onDocument(Cursor $cursor, $document);
}