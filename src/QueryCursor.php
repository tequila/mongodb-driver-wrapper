<?php

namespace Tequila\MongoDB;

use MongoDB\BSON\Unserializable;

class QueryCursor extends Cursor
{
    /**
     * @var DocumentListenerInterface[]
     */
    private $documentListeners = [];

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
        if ($document && $document !== $this->current) {
            $decoratedDocument = null;
            foreach ($this->documentListeners as $listener) {
                $listenerResult = $listener->onDocument($this, $decoratedDocument ?: $document);
                $decoratedDocument = $listenerResult ?: $decoratedDocument;
            }

            $this->current = $document;

            if ($decoratedDocument) {
                return $decoratedDocument;
            }
        }

        return $document;
    }

    /**
     * @param DocumentListenerInterface $listener
     */
    public function addDocumentListener(DocumentListenerInterface $listener)
    {
        $this->documentListeners[] = $listener;
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