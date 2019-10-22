<?php

namespace iLUB\Plugins\CleanUpSessions\Helper;

/**
 * Class CleanUpSessionsDBAccess
 * This class is responsible for the interaction between the database and the plugin
 */
use ilDB;
use ilCleanUpSessionsPlugin;

class CleanUpSessionsDBAccess implements cleanUpSessionsDBInterface
{

    /**
     * @var ilDB
     */
    protected $db;

    protected $deleted_anons;
    protected $remaining_anons;
    protected $all_remaining_sessions;

    /**
     * @var DIC
     */
    protected $DIC;

    /**
     * CleanUpSessionsDBAccess constructor.
     * @param      $dic
     * @param null $db
     * @throws \Exception
     */

    public function __construct($dic_param = null, $db_param = null, $log_param = null, $stream_param = null)
    {

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
     * @return int
     */
    public function expiredAnonymousUsers()
    {
        $thresholdBoundary = $this->getExpirationValue();
        $sql               = "SELECT * FROM usr_session WHERE user_id = 13 AND ctime < %s";
        $set               = $this->db->queryF($sql, ['integer'], [$thresholdBoundary]);

        $counter = 0;
        while ($rec = $this->db->fetchAssoc($set)) {
            $counter++;
        }

        return $counter;
    }

    /**
     * Returns the set expiration threshold set in the config
     * @return mixed
     */
    public function getExpirationValue()
    {
        $sql   = "SELECT expiration FROM clean_ses_cron";
        $query = $this->db->query($sql);
        $rec   = $this->db->fetchAssoc($query);

        return $rec['expiration'];
    }

    /**
     * Delets all the expired anonymous sessions from the DB and logs the
     * remaining non-expired anonymous sessions.
     */
    public function removeAnonymousSessionsOlderThanExpirationThreshold()
    {
        $all = $this->allAnonymousSessions();
        $sql = "DELETE FROM usr_session WHERE user_id = 13 AND ctime < %s";
        $this->db->manipulateF($sql, ['integer'], [$this->getThresholdBoundary()]);
        $this->remaining_anons = $this->allAnonymousSessions();
        $this->deleted_anons   = $all - $this->remaining_anons;
        $this->logtoDB();

    }

    /**
     * Logs all anonymous sessions to the log ilCleanUpSessionsPlugin::LOG_DESTINATION and returns the number of
     * all active anonymous sessions
     * @return int
     */
    public function allAnonymousSessions()
    {

        $sql     = "SELECT * FROM usr_session WHERE user_id = 13";
        $query   = $this->db->query($sql);
        $counter = 0;
        while ($rec = $this->db->fetchAssoc($query)) {
            $counter++;
        }

        return $counter;
    }

    /**
     * Returns the latest value in unix system time format, that is considered non-expired. All values
     * below the returned one are considered expired.
     * @return float|int
     */
    public function getThresholdBoundary()
    {
        $currentTime         = time();
        $expirationThreshold = $this->getExpirationValue();
        return $currentTime - $expirationThreshold * 60;
    }

    /**
     * Updates an entry determined by id with new information
     * @param bool $as_obj
     */
    public function updateExpirationValue($expiration)
    {
        $this->db->manipulate('UPDATE ' . ilCleanUpSessionsPlugin::TABLE_NAME . ' SET' .
            ' expiration = ' . $this->db->quote($expiration, 'integer') . ';'
        );
    }

    /**
     * Removes the table from DB after uninstall is triggered.
     */
    public function removePluginTableFromDB()
    {
        $sql = "DROP TABLE " . ilCleanUpSessionsPlugin::TABLE_NAME;
        $this->db->query($sql);

        $sql = "DROP TABLE " . ilCleanUpSessionsPlugin::LOG_TABLE;
        $this->db->query($sql);
    }

    public function logToDB()
    {
        $timestamp                    = time();
        $date                         = date('Y-m-d H:i:s', $timestamp);
        $this->all_remaining_sessions = $this->getAllSessions();
        $this->db->insert(ilCleanUpSessionsPlugin::LOG_TABLE, array(
            'timestamp'              => array('integer', $timestamp),
            'date'                   => array('datetime', $date),
            'deleted_anons'          => array('integer', $this->deleted_anons),
            'remaining_anons'        => array('integer', $this->remaining_anons),
            'all_remaining_sessions' => array('integer', $this->all_remaining_sessions)
        ));

    }

    public function getAllSessions()
    {
        $sql   = "SELECT count(*) FROM usr_session";
        $query = $this->db->query($sql);
        $rec   = $this->db->fetchAssoc($query);

        return $rec['count(*)'];
    }

}