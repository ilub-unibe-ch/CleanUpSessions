<?php


use iLUB\Plugins\CleanUpSessions\Helper\cleanUpSessionsDBAccess;
use PHPUnit\Framework\TestCase;

require dirname(__DIR__).'/src/helper/cleanUpSessionsDBAccess.php';

class DBAccessTest extends TestCase {

	protected $mockDBInterface;
	protected $mockLogger;
	protected $mockStreamHandler;
	protected $mockDIC;
	protected $RunSync;
	protected $mockDBAccess;
	protected $mockDB;

	protected $DBAccess;


	protected function setUp(): void {
		$this->mockLogger = Mockery::instanceMock(Monolog\Logger::class);
		$this->mockStreamHandler = Mockery::instanceMock(Monolog\Handler\StreamHandler::class);
		$this->mockDIC = Mockery::mock(ILIAS\DI\Container::class);
		$this->mockDB = Mockery::mock(ilDBInterface::class);
	}

	public function test_removeAnonymousSessionsOlderThanExpirationThreshold() {

		$this->mockLogger->shouldReceive("pushHandler");
        $mockDBStatement = Mockery::mock(ilDBStatement::class);
		$this->mockLogger->shouldReceive("info")->with("0 anonymous session(s) have been deleted");
		$this->mockLogger->shouldReceive("info")->with("There are 0 non-expired anonymous sessions remaining");
		$this->mockDB->shouldReceive("query")->with("SELECT * FROM usr_session WHERE user_id = 13");
		$this->mockDB->shouldReceive("query")->with("SELECT expiration FROM clean_ses_cron")->andReturn($mockDBStatement);
        $this->mockDB->shouldReceive("fetchAssoc")->with($mockDBStatement)->andReturn(['expiration' =>1]);
        $this->mockDB->shouldReceive("fetchAssoc")->times(2);
		$this->mockDB->shouldReceive("manipulateF")->times(1);

		$this->DBAccess = new cleanUpSessionsDBAccess($this->mockDIC, $this->mockDB, $this->mockLogger, $this->mockStreamHandler);
		$this->DBAccess->removeAnonymousSessionsOlderThanExpirationThreshold();
        self::assertTrue(true);
	}


	public function test_allAnonymousSessions() {
		$this->mockLogger->shouldReceive("pushHandler");
		$this->mockDB->shouldReceive("query")->with("SELECT * FROM usr_session WHERE user_id = 13");
		$this->mockDB->shouldReceive("fetchAssoc")->times(1);

		$this->DBAccess = new cleanUpSessionsDBAccess($this->mockDIC, $this->mockDB, $this->mockLogger, $this->mockStreamHandler);
		$this->DBAccess->allAnonymousSessions();
        self::assertTrue(true);

	}

	public function test_expiredAnonymousUsers() {
        $mockDBStatement = Mockery::mock(ilDBStatement::class);
		$this->mockLogger->shouldReceive("pushHandler");
		$this->mockDB->shouldReceive("query")->with("SELECT expiration FROM clean_ses_cron")->andReturn($mockDBStatement);
		$this->mockDB->shouldReceive("query")->with("SELECT * FROM usr_session WHERE user_id = 13 AND ctime < %s");
        $this->mockDB->shouldReceive("fetchAssoc")->with($mockDBStatement)->andReturn(['expiration' =>1]);
		$this->mockDB->shouldReceive("fetchAssoc")->times(1);
		$this->mockDB->shouldReceive("queryF")->times(1);

		$this->DBAccess = new cleanUpSessionsDBAccess($this->mockDIC, $this->mockDB, $this->mockLogger, $this->mockStreamHandler);
		$this->DBAccess->expiredAnonymousUsers();
        self::assertTrue(true);

	}

	public function test_getExpirationValue() {
        $mockDBStatement = Mockery::mock(ilDBStatement::class);
		$this->mockLogger->shouldReceive("pushHandler");
		$this->mockDB->shouldReceive("query")->with("SELECT expiration FROM clean_ses_cron")->andReturn($mockDBStatement);
        $this->mockDB->shouldReceive("fetchAssoc")->with($mockDBStatement)->andReturn(['expiration' =>1]);

		$this->DBAccess = new cleanUpSessionsDBAccess($this->mockDIC, $this->mockDB, $this->mockLogger, $this->mockStreamHandler);
		$this->DBAccess->getExpirationValue();
        self::assertTrue(true);

	}


	public function tearDown(): void {
		Mockery::close();
	}
}
