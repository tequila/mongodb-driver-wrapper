<?php

namespace Tequila\MongoDB;

trait ExecutionTimeTrait
{
    /**
     * @var int
     */
    private $executionTimeMS;

    /**
     * If set for this instance, returns time in milliseconds,
     * spent for the execution of a query, command or bulk write.
     * Returns null otherwise. @see setExecutionTimeMS()
     * @return int|null
     */
    public function getExecutionTimeMS()
    {
        return $this->executionTimeMS;
    }

    /**
     * @param int $executionTimeMS Time, spent for the execution of a query, command or bulk write
     */
    public function setExecutionTimeMS($executionTimeMS)
    {
        $this->executionTimeMS = $executionTimeMS;
    }
}