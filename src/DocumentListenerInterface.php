<?php

namespace Tequila\MongoDB;

interface DocumentListenerInterface
{
    public function onDocument($document);
}