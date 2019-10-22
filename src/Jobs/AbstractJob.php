<?php

namespace iLUB\Plugins\CleanUpSessions\Jobs;

use ilCronJob;

/**
 * Class AbstractJob
 * @package iLUB\Plugins\CleanUpSessions\Jobs
 */
abstract class AbstractJob extends ilCronJob
{

    /**
     * @param string $message
     */
    protected function log($message)
    {
        $this->ilLog()->write($message);
    }
}
