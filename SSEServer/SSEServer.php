<?php
namespace SSEServer;

use SSEServer\Event;
use SSEServer\Events;
use SSEServer\EventInterface;
use SSEServer\SSEControllerInterface;

class SSEServer {

    private $controller;
    private $debug;
    private $pingInterval =     1;
    private $cycleTime =        1000000;
    private $currentCycle =     0;

    function __construct(SSEControllerInterface $controller, Bool $debug = false){
        $this->controller = $controller;
        $this->debug = $debug;
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        set_time_limit(0);
    }

    public function run(){

        while (true) {
            try{
                $this->cycle();
            }catch(\Throwable $e){ 
                $debugEvent = new Event("debug", Array("error" => $e));
                $this->sendEvents(new Events($debugEvent));
            }
            
            $this->endCycle();
            if(connection_aborted()){
                $this->cleanUp();
                break;
            }
        }
    }

    private function cycle(): Void{

        $debugEvent = false;

        try{

            $this->controller->cycle();
            $events = $this->controller->getEvents();
            $this->controller->cleanUp();

        }catch(\Throwable $e){

            $debugEvent = new Event("debug", Array("error" => $e->getMessage()));

        }finally{

            if($debugEvent !== false){
                if(empty($events) || get_class($events) != "Events"){
                    $events = new Events(Array($debugEvent));
                }else{
                    $events->push($debugEvent);
                }
            }
        }
        $this->sendEvents($events);
    }

    private function sendEvents(Events $events): Void{

        if ($this->currentCycle % $this->pingInterval == 0 ){

            $pingEvent = new Event("ping", Array("t" => round(microtime(true) * 1000)));
            $pingEvent->send();

        }

        foreach($events->getEvents() as $event){

            if(strtolower($event->getType()) == "debug" && !$this->debug){
                continue;
            }

            $event->send();
        }
    }

    private function endCycle(): Void{
        @ob_end_flush();
        @flush();
        $this->currentCycle += 1;
        usleep($this->cycleTime);
    }

    public function setPingInterval(Int $interval){
        $this->pingInterval = $interval;
    }

    public function setCycleTime(Float $seconds){
        $this->cycleTime = $seconds * 1000000;
    }
}