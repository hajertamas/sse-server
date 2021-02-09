<?php
namespace SSEServer;

use SSEServer\Event;
use SSEServer\Events;
use SSEServer\EventInterface;
use SSEServer\SSEControllerInterface;

/**
 * Main class which is used to set up an event stream (Server Sent Events)
 * 
 * Using this will set Content-type header to text/event-stream, Cache-control header to no-cache
 * and script execution time limit to 0 (unlimited).
 * 
 * Pass your custom controller in to the constructor of this class and profit.
 * 
 * @version     1.0.0
 * @package     SSEServer
 * @author      Tamás Hájer <htrnfr@gmail.com>
 */
class SSEServer {

    /**
     * The controller object which should detect & store new events & pass them to this object
     * @var SSEControllerInterface
     */
    private $controller;

    /**
     * Turns debug mode on/off. If on, events with type "debug" will be sent if there are any,
     * otherwise they are ignored and not sent
     * @var Bool
     */
    private $debug;

    /**
     * Sets after how many cycles to send a "ping" event with current timestamp
     * @var Int
     */
    private $pingInterval =     1;

    /**
     * Sets wheter the server should send ping events or not
     * @var Bool
     */
    private $sendPings =        true;
    /**
     * Sets the interval between cycles in microseconds
     * @var Float
     */
    private $cycleTime =        1000000;

    /**
     * Cycle counter
     * @var Int
     */
    private $currentCycle =     1;

    /**
     * @param SSEControllerInterface $controller The controller object which should detect & store new events & pass them to this object.
     * @param Bool $debug Debug mode switch. If false, events with type "debug" will not be sent. Event type is determined by EventInterface->getType():String
     */
    function __construct(SSEControllerInterface $controller, Bool $debug = false){
        $this->controller = $controller;
        $this->debug = $debug;
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Pragma: no-cache');
        ignore_user_abort(true);
        set_time_limit(0);
    }

    /**
     * Starts the loop
     * 
     * This will run until the connection to the script calling this is aborted,
     * meaning that loading the page containing this script will never be completed.
     */
    public function run(){

        while (true) {

            if(connection_aborted() == 1){
                break;
            }

            try{
                $this->cycle();
            }catch(\Throwable $e){ 
                $debugEvent = new Event("debug", Array("error" => $e));
                $this->sendEvents(new Events(Array($debugEvent)));
            }
            
            $this->endCycle();
        }

        $this->disconnect();
        exit;
    }

    /**
     * Communicates with the controller object via the implemented Interface methods.
     * Cycles the controller, gets the Events from the controller then instructs the
     * controller to clean itself up. Passes the new Events object received from the controller
     * to $this->sendEvents() function
     * 
     * This function will be executed each cycle.
     */
    private function cycle(): Void{

        //Set as false for checking
        $debugEvent = false;
        $events = new Events;
        try{

            //Send connection confirmed event at first cycle, or disconnect just does not work.
            if($this->currentCycle === 1){
                $connected = new Event("connection", "connected");
                $connected->send();
            }
            //Execute the cycle function of the controller
            $this->controller->cycle($this->currentCycle);

            //Get the current events from the controller
            $events->merge($this->controller->getEvents());

            //Tell the controller it can clean up itself
            $this->controller->cleanUp();

        }catch(\Throwable $e){

            //Make new debug event with error message
            $debugEvent = new Event("debug", Array("error" => $e->getMessage()));

        }finally{

            //Check if we got a debug event
            if($debugEvent !== false){
                
                //Push the debug event into the Events object
                $events->push($debugEvent);
            }
        }

        //Send all events
        $this->sendEvents($events);
    }

    /**
     * Calls the send() function on all EventInterface objects inside a SSEServer\Events object.
     * @param Events $events Events object containing the events to be sent.
     */
    private function sendEvents(Events $events): Void{

        //Check if we should send a ping event in the current cycle
        if ($this->sendPings && $this->currentCycle % $this->pingInterval == 0){

            //Construct the ping Event
            $pingEvent = new Event("ping", round(microtime(true) * 1000));

            //Send the pign event
            $pingEvent->send();
        }

        //Iterate through the events in the Events object

        foreach($events->getEvents() as $event){

            //Check if message type is debug and if debug mode is on
            if(strtolower($event->getType()) == "debug" && !$this->debug){
                //Skip to next cycle if debug mode is off and event type is "debug"
                continue;
            }

            //Call the send function of the EventInterface object
            $event->send();
        }
    }

    /**
     * Used to end a cycle, send all buffered output & wait until the next cycle should be executed
     */
    private function endCycle(): Void{
        //@ used to mute the notices thrown by ob_end_flush which can greatly increase network usage.
        @ob_end_flush();
        @flush();
        
        //Update the cycle counter
        $this->currentCycle += 1;

        //Wait until the next cycle should be executed
        usleep($this->cycleTime);
    }

    private function disconnect(): Void{
        $this->controller->cleanUp();
        $this->controller->disconnect();
    }

    /**
     * Sets after how many cycles to send a "ping" event with data current time (milliseconds since epoch as int)
     * @param Int $interval Cycle count for each ping event
     */
    public function setPingInterval(Int $interval){
        $this->pingInterval = $interval;
    }

    /**
     * Sets wheter the server should send "ping" events
     * @param Bool $send Switch
     */
    public function setSendPings(Bool $sendPings){
        $this->sendPings = $sendPings;
    }

    /**
     * Sets the interval between cycles
     * @param Float $seconds Seconds between each cycle
     */
    public function setCycleTime(Float $seconds){
        $this->cycleTime = $seconds * 1000000;
    }
}