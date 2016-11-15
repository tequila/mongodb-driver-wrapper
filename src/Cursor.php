<?php

namespace Tequila\MongoDB;

use Tequila\MongoDB\Exception\LogicException;

class Cursor implements CursorInterface
{
    /**
     * @var \MongoDB\Driver\Cursor
     */
    private $wrappedCursor;

    /**
     * @var \Generator
     */
    private $generator;

    /**
     * @param \MongoDB\Driver\Cursor $wrappedCursor
     */
    public function __construct(\MongoDB\Driver\Cursor $wrappedCursor)
    {
        $this->wrappedCursor = $wrappedCursor;
        $this->generator = $this->createGenerator();
    }

    public function current()
    {
        return $this->generator->current();
    }

    public function key()
    {
        return $this->generator->key();
    }

    public function next()
    {
        $this->generator->next();
    }

    public function rewind()
    {
        if (!$this->generator->valid()) {
            throw new LogicException('Cursors cannot yield multiple iterators');
        }
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