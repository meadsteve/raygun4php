<?php
namespace MeadSteve\Raygun4php;

class EnvironmentMessage
{
    public $utcOffset;

    public function __construct()
    {
        if (ini_get('date.timezone'))
        {
            $this->utcOffset = @date('Z') / 3600;
        }
    }
}