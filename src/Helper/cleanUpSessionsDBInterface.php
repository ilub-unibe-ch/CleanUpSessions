<?php



namespace iLUB\Plugins\CleanUpSessions\Helper;


interface CleanUpSessionsDBInterface {
	//All methods declared in an interface must be public


	/**
	 * Logs all anonymous sessions to the log ilCleanUpSessionsPlugin::LOG_DESTINATION and returns the number of
	 * all active anonymous sessions
	 *
	 * @return int
	 */
	public function allAnonymousSessions();

	/**
	 * Logs all expired anonymous sessions to the log ilCleanUpSessionsPlugin::LOG_DESTINATION and returns the number of
	 * all expired anonymous sessions
	 *
	 * @return int
	 */
	public function expiredAnonymousUsers();

	/**
	 * Returns the set expiration threshold set in the config
	 *
	 * @return mixed
	 */
	public function getExpirationValue();

	/**
	 * Delets all the expired anonymous sessions from the DB and logs the
	 * remaining non-expired anonymous sessions.
	 */
	public function removeAnonymousSessionsOlderThanExpirationThreshold();

	/**
	 * Returns the latest value in unix system time format, that is considered non-expired. All values
	 * below the returned one are considered expired.
	 *
	 * @return float|int
	 */
	public function getThresholdBoundary();

	/**
	 * Updates an entry determined by id with new information
	 *
	 * @param bool $as_obj
	 */
	public function updateExpirationValue($expiration);

	/**
	 * Removes the table from DB after uninstall is triggered.
	 */
	public function removePluginTableFromDB();
}