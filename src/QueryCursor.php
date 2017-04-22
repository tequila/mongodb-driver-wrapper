<?php

namespace Tequila\MongoDB;

use MongoDB\BSON\Unserializable;

class QueryCursor extends Cursor
{
    /**
     * @var DocumentListenerInterface|null
     */
    private $documentListener;

    /**
     * @var string
     */
    private $namespace;

    /**
     * @var bool|object|array|Unserializable
     */
    private $current = false;

    /**
     * @inheritdoc
     */
    public function current()
    {
        $document = parent::current();
        if (null !== $this->documentListener && $document !== $this->current) {
            $this->documentListener->onDocument($this, $document);
        }
        $this->current = $document;

        return $document;
    }

    /**
     * @param DocumentListenerInterface $listener
     */
    public function setDocumentListener(DocumentListenerInterface $listener)
    {
        $this->documentListener = $listener;
    }

    /**
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * @param string $namespace
     */
    public function setNamespace($namespace)
    {
        $this->namespace = (string)$namespace;
    }
}