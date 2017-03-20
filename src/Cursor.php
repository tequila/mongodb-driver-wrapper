<?php

namespace Tequila\MongoDB;

use Tequila\MongoDB\Exception\LogicException;

class Cursor implements \Iterator
{
    use ExecutionTimeTrait;

    /**
     * @var \MongoDB\Driver\Cursor
     */
    private $wrappedCursor;

    /**
     * @var \Generator
     */
    private $generator;
    
    /**
     * @var bool
     */
    private $iterationStarted = false;

    /**
     * @var DocumentListenerInterface|null
     */
    private $documentListener;

    /**
     * @param \MongoDB\Driver\Cursor $wrappedCursor
     */
    public function __construct(\MongoDB\Driver\Cursor $wrappedCursor)
    {
        $this->wrappedCursor = $wrappedCursor;
        $this->generator = $this->createGenerator();
    }

    /**
     * @return array|object|\MongoDB\BSON\Unserializable
     */
    public function current()
    {
        return $this->generator->current();
    }

    /**
     * @return int
     */
    public function key()
    {
        return $this->generator->key();
    }

    /**
     * @return array|\MongoDB\BSON\Unserializable|object
     */
    public function next()
    {
        $this->generator->next();

        $document = $this->current();

        if (null !== $this->documentListener) {
            $this->documentListener->onDocument($this, $document);
        }

        return $document;
    }

    public function rewind()
    {
        if ($this->iterationStarted) {
            throw new LogicException('Cursors cannot yield multiple iterators');
        }
        $this->iterationStarted = true;
    }

    public function valid()
    {
        return $this->generator->valid();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->wrappedCursor->getId();
    }

    /**
     * @inheritdoc
     */
    public function getServer()
    {
        return $this->wrappedCursor->getServer();
    }

    /**
     * @inheritdoc
     */
    public function isDead()
    {
        return $this->wrappedCursor->isDead();
    }

    /**
     * @inheritdoc
     */
    public function setTypeMap(array $typeMap)
    {
        $this->wrappedCursor->setTypeMap($typeMap);
    }

    /**
     * @param DocumentListenerInterface $listener
     */
    public function setDocumentListener(DocumentListenerInterface $listener)
    {
        $this->documentListener = $listener;
    }

    /**
     * @inheritdoc
     */
    public function toArray()
    {
        return iterator_to_array($this);
    }

    /**
     * @return \Generator
     */
    private function createGenerator()
    {
        foreach ($this->wrappedCursor as $document) {
            yield $document;
        }
    }
}
