<?php
declare(strict_types=1);
namespace iLUB\Plugins\CleanUpSessions\Jobs;

use Exception;
use ilCronJob;
use iLUB\Plugins\CleanUpSessions\Helper\CleanUpSessionsDBAccess;
use ILIAS\DI\Container;
use ILIAS\Cron\Schedule\CronJobScheduleType;

/**
 * Class RunSync
 * 
 * This class has to run the Cron Job
 *
 * @package iLUB\Plugins\CleanUpSessions\Jobs
 */
class RunSync extends AbstractJob {


	protected Container $dic;

	protected \ilCronJobResult$job_result;
	protected cleanUpSessionsDBAccess $db_access;

    /**
     * @throws Exception
     */
    public function __construct(?\ilCronJobResult $job_result = null, cleanUpSessionsDBAccess $db_access = null) {
        if($job_result == null){
            $this->job_result = new \ilCronJobResult();
        }else {
            $this->job_result = $job_result;
        }
        if($db_access == null){
            $this->db_access = new cleanUpSessionsDBAccess();
        }else {
            $this->db_access = $db_access;
        }
    }


	public function getId(): string {
		return get_class($this);
	}

	public function hasAutoActivation(): bool {
		return true;
	}

	public function hasFlexibleSchedule(): bool {
		return true;
	}

	public function getDefaultScheduleType(): CronJobScheduleType {
		return CronJobScheduleType::SCHEDULE_TYPE_DAILY;
	}

	public function getDefaultScheduleValue(): int {
		return 1;
	}

	public function getJobResult(): \ilCronJobResult {
		return $this->job_result;
	}

	public function getDBAccess(): cleanUpSessionsDBAccess {
		return $this->db_access;
	}

	public function run(): \ilCronJobResult {
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
    public function getTitle() : string
    {
       return "CleanUpSessions Cronjob";
    }

    public function getDescription() : string
    {
        return "deletes old anonymous sessions";
    }
}
