<?php

use \Mockery as m;

class ClientTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mockery\MockInterface
     */
    protected $mockMessageBuilder;

    /**
     * @var Mockery\MockInterface
     */
    protected $mockMessageSender;

    /**
     * @var \MeadSteve\Raygun4php\Client
     */
    protected $testedClient;

    public function tearDown()
    {
        m::close();
    }

    public function setUp()
    {
        $this->mockMessageBuilder = m::mock('\MeadSteve\Raygun4php\MessageBuilder');
        $this->mockMessageSender = m::mock('\MeadSteve\Raygun4php\Senders\MessageSender');

        $this->testedClient = new \MeadSteve\Raygun4php\Client(
          $this->mockMessageBuilder,
          $this->mockMessageSender
        );
    }

    public function testSendException_CallsMessageBuilderInSimpleCase()
    {
        $exception = new \Exception("Hello Raygun world");

        $mockMessageToSend = m::mock('MeadSteve\Raygun4php\Message');
        $this->mockMessageBuilder
            ->shouldReceive("BuildMessage")
            ->times(1)
            ->with($exception, null)
            ->andReturn($mockMessageToSend);

        $this->mockMessageSender
            ->shouldReceive("Send")
            ->times(1)
            ->with($mockMessageToSend);


        $this->testedClient->SendException($exception);
    }

    public function testSendException_PassesTagsToMessageBuilder()
    {
        $exception = new \Exception("Hello Raygun world");
        $tags = array("Hello", "World");

        $mockMessageToSend = m::mock('MeadSteve\Raygun4php\Message');
        $this->mockMessageBuilder
            ->shouldReceive("BuildMessage")
            ->times(1)
            ->with($exception, null)
            ->andReturn($mockMessageToSend);

        $this->mockMessageBuilder
            ->shouldReceive("AddTagsToMessage")
            ->times(1)
            ->with($mockMessageToSend, $tags);

        $this->mockMessageSender
            ->shouldReceive("Send")
            ->with($mockMessageToSend);


        $this->testedClient->SendException($exception, $tags);
    }

    public function testSendException_PassesCustomDataToMessageBuilder()
    {
        $exception = new \Exception("Hello Raygun world");
        $customData = array("Hello" => "World");

        $mockMessageToSend = m::mock('MeadSteve\Raygun4php\Message');
        $this->mockMessageBuilder
            ->shouldReceive("BuildMessage")
            ->times(1)
            ->with($exception, null)
            ->andReturn($mockMessageToSend);

        $this->mockMessageBuilder
            ->shouldReceive("AddUserCustomDataToMessage")
            ->times(1)
            ->with($mockMessageToSend, $customData);

        $this->mockMessageSender
            ->shouldReceive("Send")
            ->with($mockMessageToSend);


        $this->testedClient->SendException($exception, null, $customData);
    }
}
 