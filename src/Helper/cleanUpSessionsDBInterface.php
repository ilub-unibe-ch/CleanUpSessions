<?php

namespace iLUB\Plugins\CleanUpSessions\Helper;

interface CleanUpSessionsDBInterface
{
    //All methods declared in an interface must be public

    /**
     * returns the number of all active anonymous sessions
     * @return int
     */
    public function allAnonymousSessions();

    /**
     * returns the number of all expired anonymous sessions
     * @return int
     */
    public function expiredAnonymousUsers();

    /**
     * Returns the set expiration threshold set in the config
     * @return mixed
     */
    public function getExpirationValue();

    /**
     * Delets all the expired anonymous sessions from the DB and
     * writes a log in the DB
     */
    public function removeAnonymousSessionsOlderThanExpirationThreshold();

    /**
     * Returns the latest value in unix system time format, that is considered non-expired. All values
     * below the returned one are considered expired.
     * @return float|int
     */
    public function getThresholdBoundary();

    /**
     * Updates an entry determined by id with new information
     * @param bool $as_obj
     */
    public function updateExpirationValue($expiration);

    /**
     * Removes the table from DB after uninstall is triggered.
     */
    public function removePluginTableFromDB();

    /**
     * logs to database: how many anonymous sessions were deleted and how many sessions were active in the last 5/15/60 Minutes
     */

    public function logToDB();

    /**
     * returns the count of all  active sessions
     * @return  mixed
     */
    public function getAllSessions();

    /**
     * returns how many Users were active during the two timestamps
     * @param $timeEarly
     * @param $timeLate
     * @return mixed
     */
    public function getSessionsBetween($timeEarly, $timeLate);
}