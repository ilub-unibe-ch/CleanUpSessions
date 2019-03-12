<?php
/**
 * Created by PhpStorm.
 * User: kaufmann
 * Date: 05.02.19
 * Time: 09:55
 */


require_once __DIR__ . "/../vendor/autoload.php";


use Mockery\ExpectationInterface;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use  iLUB\Plugins\CleanUpSessions\Helper\cleanUpSessionsDBAccess;
use PHPUnit\Framework\TestCase;


class DBAccessTest extends PHPUnit_Framework_TestCase {
	protected $mockDBInterface;


	protected $mockLogger;
	protected $mockStreamHandler;
	protected $mockDIC;
	protected $RunSync;
	protected $mockDBAccess;
	protected $mockDB;

	protected $DBAccess;


	protected function setUp() {

		//$this->mockDBAccess=Mockery::mock(iLUB\Plugins\CleanUpSessions\Helper\cleanUpSessionsDBAccess::class);
		$this->mockLogger = Mockery::instanceMock(Monolog\Logger::class);
		$this->mockStreamHandler = Mockery::instanceMock(Monolog\Handler\StreamHandler::class);
		$this->mockDIC = Mockery::mock(Pimple\Container::class);
		$this->mockDB = Mockery::mock(ilDB::class);

	}

	public function test_removeAnonymousSessionsOlderThanExpirationThreshold() {

		$this->mockLogger->shouldReceive("pushHandler");

		$this->mockLogger->shouldReceive("info")->with("access all anonymous users... ")->times(2);
		$this->mockLogger->shouldReceive("info")->with("0 anonymous session(s) have been deleted");
		$this->mockLogger->shouldReceive("info")->with("There are 0 non-expired anonymous sessions remaining");
		$this->mockDB->shouldReceive("query")->with("SELECT * FROM usr_session WHERE user_id = 13");
		$this->mockDB->shouldReceive("query")->with("SELECT expiration FROM clean_ses_cron");
		$this->mockDB->shouldReceive("fetchAssoc")->times(3);
		$this->mockDB->shouldReceive("manipulateF")->once;

		$this->DBAccess = new cleanUpSessionsDBAccess($this->mockDIC, $this->mockDB, $this->mockLogger, $this->mockStreamHandler);
		$this->DBAccess->removeAnonymousSessionsOlderThanExpirationThreshold();
	}


	public function test_allAnonymousSessions() {
		$this->mockLogger->shouldReceive("pushHandler");
		$this->mockLogger->shouldReceive("info")->with("access all anonymous users... ")->once();
		$this->mockDB->shouldReceive("query")->with("SELECT * FROM usr_session WHERE user_id = 13");
		$this->mockDB->shouldReceive("fetchAssoc")->once;
		$this->mockDB->shouldReceive("manipulateF")->once;

		$this->DBAccess = new cleanUpSessionsDBAccess($this->mockDIC, $this->mockDB, $this->mockLogger, $this->mockStreamHandler);
		$this->DBAccess->allAnonymousSessions();

	}

	public function test_expiredAnonymousUsers() {
		$this->mockLogger->shouldReceive("pushHandler");
		$this->mockDB->shouldReceive("query")->with("SELECT expiration FROM clean_ses_cron");
		$this->mockDB->shouldReceive("query")->with("SELECT * FROM usr_session WHERE user_id = 13 AND ctime < %s");
		$this->mockDB->shouldReceive("fetchAssoc")->once;
		$this->mockDB->shouldReceive("queryF")->once;

		$this->DBAccess = new cleanUpSessionsDBAccess($this->mockDIC, $this->mockDB, $this->mockLogger, $this->mockStreamHandler);
		$this->DBAccess->expiredAnonymousUsers();

	}

	public function test_getExpirationValue() {
		$this->mockLogger->shouldReceive("pushHandler");
		$this->mockDB->shouldReceive("query")->with("SELECT expiration FROM clean_ses_cron");
		$this->mockDB->shouldReceive("fetchAssoc")->once;

		$this->DBAccess = new cleanUpSessionsDBAccess($this->mockDIC, $this->mockDB, $this->mockLogger, $this->mockStreamHandler);
		$this->DBAccess->getExpirationValue();

	}


	public function tearDown() {
		Mockery::close();
	}


}
