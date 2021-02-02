<?php 
namespace SSEServer;

use SSEServer\EventInterface;

class Events{

    /**
     * An array which is used to store EventInterface objects
     * @var Array
     */
    private $events = [];

    /**
     * @param Array $eventsArray Array containing EventInterface objects.
     */
    function __construct(Array $eventsArray = null){
        
        //Check if arg is null
        if(!is_null($eventsArray)){

            //Iterate through input array
            foreach($eventsArray as $event){

                try{

                    //Check if array element implements EventInterface
                    if($event instanceof EventInterface){

                        //Store the event
                        array_push($this->events, $event);

                        //Skip to next iteration
                        continue;
                    }

                    //Throw exception
                    throw new \Exception("Object is not an instance of EventInterface");
                    
                }catch(\Throwable $e){

                    //Create new debug event
                    $debugEvent = new Event("debug", Array("error" => $e->getMessage()));

                    //Store debug event
                    array_push($this->events, $debugEvent);
                }
            }
        }
    }

    /**
     * Pushes an event into the object.
     * @param EventInterface $event Event to be added to the collection
     */
    public function push(EventInterface $event): Void{
        array_push($this->events, $event);
    }

    /**
     * Merges two Events objects
     * @param Events $events Object to be merged with current data
     */
    public function merge(Events $events){
        foreach($events as $event){
            array_push($this->events, $event);
        }
    }

    /**
     * Returns a clone of this object & erases all stored events
     * @return Events
     */
    public function flush(): Events{
        $self = clone $this;
        $this->events = [];
        return $self;
    }

    /**
     * Returns all events stored
     * @return Array
     */
    public function getEvents(): Array{
        return $this->events;
    }
}