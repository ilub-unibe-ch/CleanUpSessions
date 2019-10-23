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
    protected $timestamp;


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

    public function __construct($dic_param = null, $db_param = null)
    {
        $this->timestamp = time();
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
     * returns the number of all expired anonymous sessions
     * @return int
     */
    public function expiredAnonymousUsers()
    {
        $thresholdBoundary = $this->getExpirationValue();
        $sql               = "SELECT Count(*) FROM usr_session WHERE user_id = 13 or user_id=0 AND ctime < %s";
        $set               = $this->db->queryF($sql, ['integer'], [$thresholdBoundary]);
        $rec               = $this->db->fetchAssoc($set);
        return $rec['Count(*)'];
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
     * Delets all the expired anonymous sessions from the DB and
     * writes a log in the DB
     */
    public function removeAnonymousSessionsOlderThanExpirationThreshold()
    {
        $all = $this->allAnonymousSessions();
        $sql = "DELETE FROM usr_session WHERE user_id = 13 or user_id=0 AND ctime < %s";
        $this->db->manipulateF($sql, ['integer'], [$this->getThresholdBoundary()]);
        $this->remaining_anons = $this->allAnonymousSessions();
        $this->deleted_anons   = $all - $this->remaining_anons;
        $this-> logtoDB();

    }

    /**
     * returns the number of all active anonymous sessions
     * @return int
     */
    public function allAnonymousSessions()
    {

        $sql     = "SELECT * FROM usr_session WHERE user_id = 13 or user_id=0";
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
        $expirationThreshold = $this->getExpirationValue();
        return $this->timestamp - $expirationThreshold * 60;
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

        $date  = date('Y-m-d H:i:s', $this->timestamp);
        $min5  = $this->getSessionsBetween($this->timestamp - 300, $this->timestamp);
        $min15 = $this->getSessionsBetween($this->timestamp - 900, $this->timestamp);
        $hour1 = $this->getSessionsBetween($this->timestamp - 3600, $this->timestamp);

        $this->all_remaining_sessions = $this->getAllSessions();
        $this->db->insert('clean_ses_log', array(
            'timestamp'                => array('integer', $this->timestamp),
            'date'                     => array('datetime', $date),
            'deleted_anons'            => array('integer', $this->deleted_anons),
            'remaining_anons'          => array('integer', $this->remaining_anons),
            'all_remaining_sessions'   => array('integer', $this->all_remaining_sessions),
            'active_during_last_5min'  => array('integer', $min5),
            'active_during_last_15min' => array('integer', $min15),
            'active_during_last_hour'  => array('integer', $hour1)

        ));

    }

    /**
     * @return mixed
     */
    public function getAllSessions()
    {
        $sql   = "SELECT count(*) FROM usr_session";
        $query = $this->db->query($sql);
        $rec   = $this->db->fetchAssoc($query);

        return $rec['count(*)'];
    }

    /**
     * @param $timeEarly
     * @param $timeLate
     * @return mixed
     */
    public function getSessionsBetween($timeEarly, $timeLate)
    {
        $sql   = "SELECT count(*) from usr_session where ctime Between '" . $timeEarly . "'and '" . $timeLate . "'";
        $query = $this->db->query($sql);
        $rec   = $this->db->fetchAssoc($query);
        return $rec['count(*)'];
    }

}