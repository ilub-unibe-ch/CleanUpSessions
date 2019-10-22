<?php

namespace iLUB\Plugins\CleanUpSessions\Jobs;

use Exception;
use ilCronJob;
use iLUB\Plugins\CleanUpSessions\Helper\cleanUpSessionsDBAccess;
use ilCleanUpSessionsPlugin;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

/**
 * Class RunSync
 * This class has to run the Cron Job
 * @package iLUB\Plugins\CleanUpSessions\Jobs
 */
class RunSync extends AbstractJob
{

    /**
     * @var
     */
    protected $dic;

    /**
     * @var \ilCronJobResult
     */
    protected $job_result;
    protected $db_access;

    /**
     * RunSync constructor.
     * @param \ilCronJobResult|null $dic_param
     * Dieses wird ausgeführt, wenn im GUI die Cron-Jobs angezeigt werden.
     */
    public function __construct(\ilCronJobResult $job_result = null, cleanUpSessionsDBAccess $db_access = null)
    {
        $this->job_result = $job_result;
        if ($this->job_result == null) {
            $this->job_result = new \ilCronJobResult();
        }
        $this->db_access = $db_access;
        if ($this->db_access == null) {
            $this->db_access = new cleanUpSessionsDBAccess();
        }
    }

    /**
     * @return string
     */
    public function getId()
    {
        return get_class($this);
    }

    /**
     * @return bool
     */
    public function hasAutoActivation()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function hasFlexibleSchedule()
    {
        return true;
    }

    /**
     * @return int
     */
    public function getDefaultScheduleType()
    {
        return ilCronJob::SCHEDULE_TYPE_DAILY;
    }

    /**
     * @return null
     */
    public function getDefaultScheduleValue()
    {
        return 1;
    }

    /**
     * @return \ilCronJobResult
     */
    public function getJobResult()
    {

        return $this->job_result;

    }

    /**
     * @return cleanUpSessionsDBAccess
     */
    public function getDBAccess()
    {

        return $this->db_access;
    }

    /**
     * @return \ilCronJobResult
     * @throws
     */
    public function run()
    {

        $jobResult = $this->getJobResult();

        try {

            $tc = $this->getDBAccess();
            $tc->removeAnonymousSessionsOlderThanExpirationThreshold();

            $jobResult->setStatus($jobResult::STATUS_OK);
            $jobResult->setMessage("Everything worked fine.");
            return $jobResult;
        } catch (Exception $e) {
            $jobResult->setStatus($jobResult::STATUS_CRASHED);
            $jobResult->setMessage("There was an error.");
            return $jobResult;
        }
    }
}
