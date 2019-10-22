<?php

require_once __DIR__ . "/../vendor/autoload.php";

use iLUB\Plugins\CleanUpSessions\Jobs\RunSync;

class RunTest extends PHPUnit_Framework_TestCase
{

    protected $mockCronJobResult;
    protected $RunSync;
    protected $mockDBAccess;

    protected function setUp()
    {

        $this->mockDBAccess      = Mockery::mock(iLUB\Plugins\CleanUpSessions\Helper\cleanUpSessionsDBAccess::class);
        $this->mockCronJobResult = Mockery::mock(\ilCronJobResult::class);
        $this->RunSync           = new RunSync($this->mockCronJobResult, $this->mockDBAccess);
    }

    public function test_getJobResult()
    {
        $result = $this->RunSync->getJobResult();
        $this->assertTrue($result InstanceOf \ilCronJobResult);

    }

    public function test_getDBAccess()
    {

        $result = $this->RunSync->getDBAccess();
        $this->assertTrue($result InstanceOf iLUB\Plugins\CleanUpSessions\Helper\CleanUpSessionsDBAccess);
    }

    public function test_run()
    {
        //Expectations
        $this->mockDBAccess->shouldReceive("removeAnonymousSessionsOlderThanExpirationThreshold")->once();
        $this->mockCronJobResult->shouldReceive("setStatus")->with($this->mockCronJobResult::STATUS_OK);
        $this->mockCronJobResult->shouldReceive("setMessage")->with("Everything worked fine.");

        //executes the code that will be tested
        $receivedResult = $this->RunSync->run();

        //Assertions
        $this->assertTrue($receivedResult InstanceOf \ilCronJobResult);
        $this->mockCronJobResult->shouldReceive("getMessage")->andReturn("Everything worked fine.");
        $receivedResult = $receivedResult->getMessage();
        $expectedResult = "Everything worked fine.";
        $this->assertEquals($expectedResult, $receivedResult);

    }

    public function tearDown()
    {
        Mockery::close();
    }

}
