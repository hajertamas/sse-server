<?php 
namespace SSEServer;

interface SSEControllerInterface{
    public function cycle(Int $currentCycle): Void;
    public function cleanUp(): Void;
    public function getEvents(): Events;
    public function disconnect(): Void;
}