<?php
require_once "autoload.php";

$client = \MeadSteve\Raygun4php\RayGun::getClient("asadfasd", true);

$client->SendException(new \Exception("ahh"));