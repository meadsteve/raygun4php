<?php
namespace MeadSteve\Raygun4php;

class Message
{
    public $OccurredOn;
    public $Details;

    public function __construct($timestamp = null)
    {
        if ($timestamp === null) {
            $timestamp = time();
        }
        $this->OccurredOn = gmdate("Y-m-d\TH:i:s", $timestamp);
        $this->Details = new MessageDetails();
    }

    public function Build($exception)
    {
        $this->Details->MachineName = gethostname();
        $this->Details->Error = new ExceptionMessage($exception);
        $this->Details->Request = new RequestMessage();
        $this->Details->Environment = new EnvironmentMessage();
        $this->Details->Client = new ClientMessage();
    }
}
