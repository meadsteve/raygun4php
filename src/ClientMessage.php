<?php
namespace MeadSteve\Raygun4php;

class ClientMessage
{
    public $name;
    public $version;
    public $clientUrl;

    public function __construct()
    {
        $this->name = "Raygun4php";
        $this->version = "1.0.1";
        $this->clientUrl = "https://github.com/MindscapeHQ/raygun4php";
    }
}
