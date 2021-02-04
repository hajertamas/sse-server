<?php 
namespace SSEServer;

/**
 * Default EventInterface implementation.
 * Sends event data as json
 */
class Event implements \SSEServer\EventInterface{

    private $type;
    private $data;

    /**
     * @param String $eventType The type of the event. This can be anything.
     * @param $data Array of data to be sent as JSON output.
     */
    function __construct(String $eventType, $data){
        $this->type = $eventType;
        $this->data = $data;
    }

    /**
     * Sends the event as output.
     */
    public function send(): Void{
        //Send event type
        echo "event: " . $this->type . "\n";

        //Send data
        echo "data: " . json_encode($this->data, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

        //Indicate event end
        echo "\n\n";
    }

    /**
     * Returns the event type
     * @return String
     */
    public function getType(): String{
        return $this->type;
    }
}