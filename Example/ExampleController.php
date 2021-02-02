<?php 
namespace Example;

use SSEServer\Event;
use SSEServer\Events;
use SSEServer\EventInterface;
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
     * in this object which won't be needed at next cycle. In this case we left it blank
     */
    public function cleanUp(): Void{

    }
}