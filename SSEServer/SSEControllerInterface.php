<?php 
namespace SSEServer;

interface SSEControllerInterface{
    public function cycle(): Void;
    public function cleanUp(): Void;
    public function getEvents(): Events;
}