<?php
declare(strict_types=1);

use iLUB\Plugins\CleanUpSessions\Helper\cleanUpSessionsDBAccess;
use iLUB\Plugins\CleanUpSessions\Jobs\RunSync;

/**
 * Class ilCleanUpSessionsPlugin
 *
 * @package
 */
class ilCleanUpSessionsPlugin extends ilCronHookPlugin {

	const PLUGIN_ID = 'clean_ses';
	const PLUGIN_NAME = 'CleanUpSessions';
	const TABLE_NAME = 'clean_ses_cron';
	const COLUMN_NAME = 'expiration';
	const DEFAULT_EXPIRATION_VALUE = 240;
	const EXPIRATION_THRESHOLD = 'expiration_threshold';
	# const IL_PLUGIN_TABLE = 'il_plugin';
	const LOG_DESTINATION = '/var/log/ilias/CleanUpSessions.log';

	protected static ilCleanUpSessionsPlugin $instance;
	protected cleanUpSessionsDBAccess $access;


	public function getPluginName(): string {
		return self::PLUGIN_NAME;
	}

	public static function getInstance(): ilCleanUpSessionsPlugin {
        if (!isset(self::$instance)) {
            global $DIC;

            $component_repository = $DIC["component.repository"] ;
			self::$instance = new self($DIC->database(), $component_repository, 'clean_ses');
		}

		return self::$instance;
	}

    /**
     * @return ilCronJob[]
     * @throws Exception
     */
	public function getCronJobInstances(): array {
		return [new RunSync()];
	}


	public function getCronJobInstance(string $a_job_id): ilCronJob {
		$a_job_id = "\iLUB\Plugins\CleanUpSessions\Jobs\RunSync";
		return new $a_job_id();
	}

    /**
     * AfterUninstall deletes the tables from the DB
     * @throws Exception
     */
	protected function afterUninstall(): void {
		$this->access = new cleanUpSessionsDBAccess();
		$this->access->removePluginTableFromDB();
	}

}
