<?php
declare(strict_types=1);
namespace iLUB\Plugins\CleanUpSessions\Helper;

/**
 * Class CleanUpSessionsDBAccess
 *
 * This class is responsible for the interaction between the database and the plugin
 *
 */
use ilCleanUpSessionsPlugin;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use ilDBInterface;
use ILIAS\DI\Container;

class CleanUpSessionsDBAccess implements cleanUpSessionsDBInterface {

	protected ilDBInterface $db;
	protected Logger $logger;
	protected StreamHandler $streamHandler;
	protected Container $DIC;

    /**
     * CleanUpSessionsDBAccess constructor. Initializes Monolog logger. Logs to root directory of the plugin.
     * @param null $dic_param
     * @param null $db_param
     * @param null $log_param
     * @param null $stream_param
     */

	public function __construct($dic_param = null, $db_param = null, $log_param = null, $stream_param = null) {
		if ($log_param == null) {
			$this->logger = new Logger("CleanUpSessionsDBAccess");
		} else {
			$this->logger = $log_param;
		}
		if ($stream_param == null) {
			$this->streamHandler = new StreamHandler(ilCleanUpSessionsPlugin::LOG_DESTINATION);
		} else {
			$this->streamHandler = $stream_param;
		}
		$this->logger->pushHandler($this->streamHandler);

		if ($dic_param == null) {
			global $DIC;
			$this->DIC = $DIC;
		} else {
			$this->DIC = $dic_param;
		}
		if ($db_param == null) {
			$this->db = $this->DIC->database();
		} else {
			$this->db = $db_param;
		}
	}

	/**
	 * Logs all expired anonymous sessions to the log ilCleanUpSessionsPlugin::LOG_DESTINATION and returns the number of
	 * all expired anonymous sessions
	 */
	public function expiredAnonymousUsers(): int {
		$thresholdBoundary = $this->getExpirationValue();
		$sql = "SELECT * FROM usr_session WHERE user_id = 13 AND ctime < %s";
		$set = $this->db->queryF($sql, ['integer'], [$thresholdBoundary]);

		$counter = 0;
		while ($rec = $this->db->fetchAssoc($set)) {
			$msg = 'Expired Users -> #' . $counter++ . '  id: ' . $rec['user_id'] . ' valid till: ' .
				date('Y-m-d - H:i:s', $rec['expires']) . "\n";
			$this->logger->info($msg);
		}

		return $counter;
	}

	/**
	 * Returns the set expiration threshold set in the config
	 *
	 * @return mixed
	 */
	public function getExpirationValue(): string {
		$sql = "SELECT expiration FROM clean_ses_cron";
		$query = $this->db->query($sql);
		$rec = $this->db->fetchAssoc($query);

		return (string) $rec['expiration'];
	}

	/**
	 * Deletes all the expired anonymous sessions from the DB and logs the
	 * remaining non-expired anonymous sessions.
	 */
	public function removeAnonymousSessionsOlderThanExpirationThreshold() {

		$all = $this->allAnonymousSessions();

		$sql = "DELETE FROM usr_session WHERE user_id = 13 AND ctime < %s";
		$this->db->manipulateF($sql, ['integer'], [$this->getThresholdBoundary()]);

		$after = $this->allAnonymousSessions();

		// Only for debugging:
		$this->logger->info($all - $after . " anonymous session(s) have been deleted");
		$this->logger->info("There are " . $after . " non-expired anonymous sessions remaining");
	}

	/**
	 * Logs all anonymous sessions to the log ilCleanUpSessionsPlugin::LOG_DESTINATION and returns the number of
	 * all active anonymous sessions
	 *
	 * @return int
	 */
	public function allAnonymousSessions(): int {

		$sql = "SELECT * FROM usr_session WHERE user_id = 13";
		$query = $this->db->query($sql);
		$counter = 0;
		while ($this->db->fetchAssoc($query)) {
			$counter++;
		}

		return $counter;
	}

	/**
	 * Returns the latest value in unix system time format, that is considered non-expired. All values
	 * below the returned one are considered expired.
	 */
	public function getThresholdBoundary(): int {
		$currentTime = time();
		$expirationThreshold = $this->getExpirationValue();
		return $currentTime - $expirationThreshold * 60;
	}

    /**
     * Updates an entry determined by id with new information
     */
    public function updateExpirationValue(int $expiration) {
		$this->db->manipulate('UPDATE ' . ilCleanUpSessionsPlugin::TABLE_NAME . ' SET' .
			' expiration = ' . $this->db->quote($expiration, 'integer') . ';'
		);
	}

	/**
	 * Removes the table from DB after uninstall is triggered.
	 */
	public function removePluginTableFromDB(): void  {
		$sql = "DROP TABLE " . ilCleanUpSessionsPlugin::TABLE_NAME;
		$this->db->query($sql);
	}
}