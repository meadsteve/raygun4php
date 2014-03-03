<?php
namespace MeadSteve\Raygun4php;

class RaygunMessage
{
    public $OccurredOn;
    public $Details;

    public function __construct($timestamp = null)
    {
        if ($timestamp === null) {
            $timestamp = time();
        }
        $this->OccurredOn = gmdate("Y-m-d\TH:i:s", $timestamp);
        $this->Details = new RaygunMessageDetails();
    }

    public function Build($exception)
    {
        $this->Details->MachineName = gethostname();
        $this->Details->Error = new RaygunExceptionMessage($exception);
        $this->Details->Request = new RaygunRequestMessage();
        $this->Details->Environment = new RaygunEnvironmentMessage();
        $this->Details->Client = new RaygunClientMessage();
    }
}