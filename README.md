# SSEServer

A small PHP library for webRTC via Server Sent Events. A nice alternative to Websockets for shared hosting. 
Build your own controller via a single simple interface & its ready to go.

---

### Installation
    $ composer require htrnf/sse-server
---

### Quick example
##### PHP
events.php:
```php
<?php

require __DIR__."/vendor/autoload.php";

use SSEServer\SSEServer;
use SSEServer\SSEControllerInterface;

class ExampleController implements SSEControllerInterface{

    private $events;

    function __construct(){
          
        //We will store our Event objects into "Events"
        $this->events = new Events;
    }

    /**
     * Each cycle while running, this function will be called.
     * This is where you should implement main logic for detecting new events
     * and populate $this->events with them using $this->events->push().
     */
    public function cycle(): Void{

        //We will randomly send a message
        $random = rand(0, 10);

        switch($random){
            case 0:
                //Push the new event into the Events object
                $this->events->push(new Event("message", Array("text" => "Hello")));
                break;
            case 1:
                $this->events->push(new Event("message", Array("text" => "Howdy?")));
                break;
            case 2:
                $this->events->push(new Event("message", Array("text" => "Do you like banana pancakes?")));
                break;
            case 3:
                $this->events->push(new Event("message", Array("text" => "Parrots are the coolest animals!")));
                break;
            case 4:
                $this->events->push(new Event("message", Array("text" => "Please go to the grocery store and get some bread.")));
                break;
            case 5:
                $this->events->push(new Event("alert", Array("text" => "You have a scheduled meeting in 5 minutes, you are already too late.")));
        }
    }

    /**
     * This function will be called each cycle after $this->cycle()
     * This function must return a SSEServer\Events object containing all events (SSEServer\Event) which
     * should be sent as output in the current cycle.
     */
    public function getEvents(): Events{
        return $this->events->flush();
    }

    /**
     * This function will be called each cycle after $this->getEvents()
     * Cleanup logic should be implemented here, e.g. unset variables stored
     * in this object which won't be needed at next cycle. In this case we left it blank.
     */
    public function cleanUp(): Void{

    }

    //Create a new instance of our custom SSEControllerInterface object
    $controller = new ExampleController();

    //Create a new SSEServer instance, pass the controller object as the first arg
    //Second arg is degubmode, if set to true, the server will send events with type "debug" if there are any.
    $server = new SSEServer($controller, true);

    //Every $cycleTime seconds the server will do a cycle (default is 1 seconds)
    $cycleTime = 1;
    $server->setCycleTime($cycleTime);

    //In every $pingInterval (2nd) cycle a ping event will be sent containing current time (milliseconds since epoch as int) (default is every cycle (1))
    $pingInterval = 2;
    $server->setPingInterval(1);

    //Run the server (loop)
    $server->run();
```
##### Javascript
```javascript
    //Create EventSource pointing to the url of our php file
    const evtSource = new EventSource("events.php");

    //Add event listener for any type of event which you implemented.
    evtSource.addEventListener("ping", (event) => { console.log(event.data); });

    evtSource.addEventListener("message", (event) => { console.log(event.data); });
```
