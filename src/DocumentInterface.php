<?php

namespace Tequila\MongoDB;

use MongoDB\BSON\ObjectID;

interface DocumentInterface
{
    /**
     * @return mixed|ObjectID
     */
    public function getId();

    /**
     * @param mixed|ObjectID $id
     */
    public function setId($id);
}