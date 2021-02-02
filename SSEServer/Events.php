<?php 
namespace SSEServer;

use SSEServer\EventInterface;

class Events{

    private $events = [];

    function __construct(Array $eventsArray = null){
        if(!is_null($eventsArray)){
            foreach($eventsArray as $event){
                try{
                    if($event instanceof EventInterface){
                        array_push($this->events, $event);
                        continue;
                    }
                    throw new \Exception("Object is not an instance of EventInterface");
                    
                }catch(\Throwable $e){
                    $debugEvent = new Event("debug", 
                        Array(
                            "error" => $e->getMessage()
                        )
                    );
                    array_push($this->events, $debugEvent);
                }
            }
        }
    }

    public function push(EventInterface $event): Void{
        array_push($this->events, $event);
    }

    public function flush(): Events{
        $self = clone $this;
        $this->events = [];
        return $self;
    }
    
    public function getEvents(): Array{
        return $this->events;
    }
}