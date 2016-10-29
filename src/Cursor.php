<?php

namespace Tequila\MongoDB;

class Cursor implements CursorInterface
{
    /**
     * @var \MongoDB\Driver\Cursor
     */
    private $wrappedCursor;

    /**
     * @param \MongoDB\Driver\Cursor $wrappedCursor
     */
    public function __construct(\MongoDB\Driver\Cursor $wrappedCursor)
    {
        $this->wrappedCursor = $wrappedCursor;
    }

    /**
     * @inheritdoc
     */
    public function getIterator()
    {
        foreach ($this->wrappedCursor as $document) {
            yield $document;
        }
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
        return iterator_to_array($this->getIterator());
    }
}