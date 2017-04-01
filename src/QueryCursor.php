<?php

namespace Tequila\MongoDB;

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
     * @inheritdoc
     */
    public function next()
    {
        $document = parent::next();
        if (null !== $this->documentListener) {
            $this->documentListener->onDocument($this, $document);
        }

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