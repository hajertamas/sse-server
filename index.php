<?php
require __DIR__."/vendor/autoload.php";

use SSEServer\SSEServer;
use Example\ExampleController;

$sseController = new ExampleController("asd");
$server = new SSEServer($sseController, true);

//Every $cycleTime seconds the server will do a cycle (default is 1 seconds)
$cycleTime = 3;
$server->setCycleTime($cycleTime);

//In every $pingInterval (2nd) cycle a ping event will be sent containing current time (milliseconds since epoch as int) (default is every cycle (1))
$pingInterval = 2;
$server->setPingInterval(1);

//Run the server (loop)
$server->run();
