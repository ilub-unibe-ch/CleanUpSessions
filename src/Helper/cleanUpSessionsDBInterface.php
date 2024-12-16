<?php
declare(strict_types=1);


namespace iLUB\Plugins\CleanUpSessions\Helper;


interface CleanUpSessionsDBInterface {
	//All methods declared in an interface must be public


	/**
	 * Logs all anonymous sessions to the log ilCleanUpSessionsPlugin::LOG_DESTINATION and returns the number of
	 * all active anonymous sessions
	 */
	public function allAnonymousSessions(): int;

	/**
	 * Logs all expired anonymous sessions to the log ilCleanUpSessionsPlugin::LOG_DESTINATION and returns the number of
	 * all expired anonymous sessions
	 */
	public function expiredAnonymousUsers(): int;

	/**
	 * Returns the set expiration threshold set in the config
	 */
	public function getExpirationValue(): string;

	/**
	 * Deletes all the expired anonymous sessions from the DB and logs the
	 * remaining non-expired anonymous sessions.
	 */
	public function removeAnonymousSessionsOlderThanExpirationThreshold();

	/**
	 * Returns the latest value in unix system time format, that is considered non-expired. All values
	 * below the returned one are considered expired.
	 */
	public function getThresholdBoundary(): int;

    /**
     * Updates an entry determined by id with new information
     */
	public function updateExpirationValue(int $expiration);

	/**
	 * Removes the table from DB after uninstall is triggered.
	 */
	public function removePluginTableFromDB();
}