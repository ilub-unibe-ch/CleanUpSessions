<?php

namespace iLUB\Plugins\CleanUpSessions\Jobs;

use Exception;
use ilCronJob;
use iLUB\Plugins\CleanUpSessions\Helper\CleanUpSessionsDBAccess;
use ilCleanUpSessionsPlugin;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;


/**
 * Class RunSync
 *
 * @package iLUB\Plugins\CleanUpSessions\Jobs
 */
class RunSync extends AbstractJob {

	/**
	 * @var logger
	 */
	protected $logger;

	protected $dic;

	/**
	 * @return string
	 */
	public function getId() {
		return get_class($this);
	}


	/**
	 * @return bool
	 */
	public function hasAutoActivation() {
		return true;
	}


	/**
	 * @return bool
	 */
	public function hasFlexibleSchedule() {
		return true;
	}


	/**
	 * @return int
	 */
	public function getDefaultScheduleType() {
		return ilCronJob::SCHEDULE_TYPE_DAILY;
	}


	/**
	 * @return null
	 */
	public function getDefaultScheduleValue() {
		return 1;
	}

	public function getLogger(String $name){
	    return new Logger($name);
    }

    public function getStreamHandler($logDestination){

	    return new StreamHandler($logDestination);
    }


    public function getJobResult(){
	    return new \ilCronJobResult();
    }

    public function getDBAccess(){
	    return new CleanUpSessionsDBAccess();
    }
	/**
	 * @return \ilCronJobResult
	 * @throws
	 */
	public function run() {
		$this->logger = $this->getLogger("CronSyncLogger");



		$this->logger->pushHandler($this->getStreamHandler(ilCleanUpSessionsPlugin::LOG_DESTINATION), Logger::DEBUG);
		$jobResult = $this->getJobResult();

		$this->logger->info("Rsync::run() \n");
		try {

			$tc = $this->getDBAccess();
			$tc->allAnonymousSessions();
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
