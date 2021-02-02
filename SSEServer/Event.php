<?php 
namespace SSEServer;

class Event implements \SSEServer\EventInterface{

    private $type;
    private $data;

    function __construct(String $eventType, Array $data){
        $this->type = $eventType;
        $this->data = $data;
    }
    public function send(): Void{
        echo "event: " . $this->type . "\n";
        echo "data: " . json_encode($this->data, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        echo "\n\n";
    }
    public function getType(): String{
        return $this->type;
    }
}