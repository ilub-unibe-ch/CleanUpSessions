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
use iLUB\Plugins\CleanUpSessions\Jobs\RunSync;
use  iLUB\Plugins\CleanUpSessions\Helper\cleanUpSessionsDBAccess;

use PHPUnit\Framework\TestCase;




class RunTest extends PHPUnit_Framework_TestCase

{
    protected $mockDBInterface;
    protected $mockRunSync;
    protected $mockCronJobResult;
    protected $mockLogger;
    protected $mockStreamHandler;
    protected $mockDIC;
    protected $RunSync;
    protected $mockDBAccess;
    protected $mockDB;


    protected function setUp() {



        $this->mockDBInterface=Mockery::instanceMock(iLUB\Plugins\CleanUpSessions\Helper\cleanUpSessionsDBInterface::class);
        $this->mockDBAccess=Mockery::mock(iLUB\Plugins\CleanUpSessions\Helper\cleanUpSessionsDBAccess::class);
        $this->mockRunSync=Mockery::mock(iLUB\Plugins\CleanUpSessions\Jobs\RunSync::class);
        $this->mockCronJobResult=Mockery::instanceMock(\ilCronJobResult::class);
        $this->mockLogger=Mockery::instanceMock(Monolog\Logger::class);
        $this->mockStreamHAndler=Mockery::instanceMock(Monolog\Handler\StreamHandler::class);
        $this->mockDIC=Mockery::mock(Pimple\Container::class);
        $this->mockDB=Mockery::mock(ilDB::class);

        //$this->mockIlPLugin=\Mockery::mock('\ilCleanUpSessionsPlugin');
       // $this->mockIlPlugin->getConfiguration->setConstantsMap(['mockilPlugin'=>['DESTINATION_LOG'=>'./Customizing/global/plugins/Services/Cron/CronHook/CleanUpSessions/app.log']]);

        $this->RunSync=new RunSync();
        $this->mockRunSync=Mockery::instanceMock(RunSync::class);
    }





    public function test_getJobResult(){
        $result=$this->RunSync::getJobResult();
        $this->assertTrue($result InstanceOf\ilCronJobResult );


    }
    /**
          public function test_getDBAccess(){
        $this->mockDBAccess->shouldReceive("getLogger")->with("CleanUpSessionsDBAccess")->andReturn($this->mockLogger);
        $result=$this->RunSync::getDBAccess();
        

        $this->assertTrue($result InstanceOf iLUB\Plugins\CleanUpSessions\Helper\CleanUpSessionsDBAccess );
    }



    public function test_DBAccess(){
        $this->mockDBAccess->shouldReceive('getLogger')->with('CronSyncLogger')->andReturn("mockLogger")->once();
        $this->mockLogger->shouldReceive("pushHandler")->with('./Customizing/global/plugins/Services/Cron/CronHook/CleanUpSessions/app.log' ,$this->mockLogger::DEBUG)->once();

        $dbAcess=new cleanUpSessionsDBAccess($this->mockDB, $this->mockDIC);

    }


    public function test_run()
    {

        $this->mockRunSync->shouldReceive("getDBAccess")->with($this->mockDB,$this->mockDIC)->andReturn($this->mockDBInterface);

        $this->mockDBAccess->shouldReceive('getLogger')->with('CronSyncLogger')->andReturn("mockLogger")->once();
        //$this->mockDBAccess->shouldReceive("pushHandler")->with('./Customizing/global/plugins/Services/Cron/CronHook/CleanUpSessions/app.log' ,$this->mockLogger::DEBUG)->once();
        $this->mockDBAccess->shouldReceive('database')->andReturn("mockDB")->once();
        //$this->mockRunSync->shouldReceive("getJobResult")->andReturn("mockCronJobResult")->once();
        //$this->mockRunSync->shouldREceive("getDBAccess")->andReturn($this->mockDBInterface)->once();
        //$this->mockDBInterface->shouldReceive("allAnonymousSessions")->once();
       // $this->mockRunSync->shouldReceive("removeAnonymousSessionsOlderThanExpirationThreshhold")->once();

        $this->mockCronJobResult->shouldReceive("setStatus")->with($this->mockCronJobResult::STATUS_OK);
        $this->mockCronJobResult->shouldReceive("setMessage")->with("Everything worked fine.");

        //executes the code that will be tested
       $receivedResult= $this->RunSync->run();

       //Assertions
        $this->assertTrue($receivedResult InstanceOf\ilCronJobResult );
        $receivedResult=$receivedResult->getMessage();
        $expectedResult="Everything worked fine.";
        $this->assertEquals( $expectedResult, $receivedResult);

    }
     */







    public function tearDown() {
        Mockery::close();
    }


}
