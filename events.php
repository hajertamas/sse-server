<?php
require __DIR__."/vendor/autoload.php";

use SSEServer\SSEServer;
use Example\ExampleController;

//Create a new instance of our custom SSEControllerInterface object
$controller = new ExampleController();

//Create a new SSEServer instance, pass the controller object as the first arg
//Second arg is degubmode, if set to true, the server will send events with type "debug" if there are any.
$server = new SSEServer($controller, true);

//Every $cycleTime seconds the server will do a cycle (default is 1 seconds)
$cycleTime = 2;
$server->setCycleTime($cycleTime);

//In every $pingInterval (2nd) cycle a ping event will be sent containing current time (milliseconds since epoch as int) (default is every cycle (1))
$pingInterval = 2;
$server->setPingInterval(1);

//Run the server (loop)
$server->run();
