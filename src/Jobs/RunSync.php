<?php

namespace iLUB\Plugins\DelUser\Jobs;

use Exception;
use ilCronJob;
use iLUB\Plugins\DelUser\Jobs\Result\AbstractResult;
use iLUB\Plugins\DelUser\Jobs\Result\ResultFactory;
use iLUB\Plugins\DelUser\Log\OriginLog;
use iLUB\Plugins\DelUser\Origin\OriginFactory;
use iLUB\Plugins\DelUser\Sync\OriginSyncFactory;
use iLUB\Plugins\DelUser\Sync\Summary\OriginSyncSummaryFactory;

/**
 * Class RunSync
 *
 * @package iLUB\Plugins\DelUser\Jobs
 */
class RunSync extends AbstractJob {

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


	/**
	 * @return AbstractResult
	 */
	public function run() {
		try {
			$OriginSyncSummaryFactory = new OriginSyncSummaryFactory();

			$OriginFactory = new OriginFactory($this->db());

			$summary = $OriginSyncSummaryFactory->cron();
			foreach ($OriginFactory->getAllActive() as $origin) {
				$originSyncFactory = new OriginSyncFactory($origin);
				$originSync = $originSyncFactory->instance();
				try {
					$originSync->execute();
				} catch (Exception $e) {

				}
				$OriginLog = new OriginLog($originSync->getOrigin());
				$OriginLog->write($summary->getSummaryOfOrigin($originSync));

				$summary->addOriginSync($originSync);
			}

            $summary->sendNotifications();

			return ResultFactory::ok("everything's fine.");
		} catch (Exception $e) {
			return ResultFactory::error("there was an error");
		}
	}
}
