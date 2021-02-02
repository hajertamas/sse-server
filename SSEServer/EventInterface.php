<?php 
namespace SSEServer;

interface EventInterface{
    public function send(): Void;
    public function getType(): String;
}