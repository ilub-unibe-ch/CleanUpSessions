<?php

require_once __DIR__ . "/../vendor/autoload.php";

use  iLUB\Plugins\CleanUpSessions\Helper\cleanUpSessionsDBAccess;

class DBAccessTest extends PHPUnit_Framework_TestCase
{
    protected $mockDBInterface;



    protected $mockDIC;
    protected $RunSync;
    protected $mockDBAccess;
    protected $mockDB;

    protected $DBAccess;

    protected function setUp()
    {

        //$this->mockDBAccess=Mockery::mock(iLUB\Plugins\CleanUpSessions\Helper\cleanUpSessionsDBAccess::class);

        $this->mockDIC           = Mockery::mock(Pimple\Container::class);
        $this->mockDB            = Mockery::mock(ilDB::class);

    }

    public function test_removeAnonymousSessionsOlderThanExpirationThreshold()
    {


        $this->mockDB->shouldReceive("query")->with("SELECT * FROM usr_session WHERE user_id = 13");
        $this->mockDB->shouldReceive("query")->with("SELECT expiration FROM clean_ses_cron");
        $this->mockDB->shouldReceive("fetchAssoc")->times(4);
        $this->mockDB->shouldReceive("manipulateF")->once;
        $this->mockDB->shouldReceive("query")->with("SELECT count(*) FROM usr_session");
        $this->mockDB->shouldReceive("insert");

        $this->DBAccess = new cleanUpSessionsDBAccess($this->mockDIC, $this->mockDB);
        $this->DBAccess->removeAnonymousSessionsOlderThanExpirationThreshold();
    }

    public function test_allAnonymousSessions()
    {

        $this->mockDB->shouldReceive("query")->with("SELECT * FROM usr_session WHERE user_id = 13");
        $this->mockDB->shouldReceive("fetchAssoc")->once;
        $this->mockDB->shouldReceive("manipulateF")->once;

        $this->DBAccess = new cleanUpSessionsDBAccess($this->mockDIC, $this->mockDB);
        $this->DBAccess->allAnonymousSessions();

    }

    public function test_expiredAnonymousUsers()
    {

        $this->mockDB->shouldReceive("query")->with("SELECT expiration FROM clean_ses_cron");
        $this->mockDB->shouldReceive("query")->with("SELECT * FROM usr_session WHERE user_id = 13 AND ctime < %s");
        $this->mockDB->shouldReceive("fetchAssoc")->once;
        $this->mockDB->shouldReceive("queryF")->once;

        $this->DBAccess = new cleanUpSessionsDBAccess($this->mockDIC, $this->mockDB);
        $this->DBAccess->expiredAnonymousUsers();

    }

    public function test_getExpirationValue()
    {

        $this->mockDB->shouldReceive("query")->with("SELECT expiration FROM clean_ses_cron");
        $this->mockDB->shouldReceive("fetchAssoc")->once;

        $this->DBAccess = new cleanUpSessionsDBAccess($this->mockDIC, $this->mockDB);
        $this->DBAccess->getExpirationValue();

    }

    public function test_logToDB(){

        $this->mockDB->shouldReceive("query")->with("SELECT count(*) FROM usr_session");
        $this->mockDB->shouldReceive("fetchAssoc")->once;
        $this->mockDB->shouldReceive("insert")->once;


        $this->DBAccess = new cleanUpSessionsDBAccess($this->mockDIC, $this->mockDB);
        $this->DBAccess->logToDB();
    }


    public function test_getAllSessions(){
        $this->mockDB->shouldReceive("query")->with("SELECT count(*) FROM usr_session");
        $this->mockDB->shouldReceive("fetchAssoc")->once;


        $this->DBAccess = new cleanUpSessionsDBAccess($this->mockDIC, $this->mockDB);
        $this->DBAccess->getAllSessions();
    }
    public function tearDown()
    {
        Mockery::close();
    }
}
