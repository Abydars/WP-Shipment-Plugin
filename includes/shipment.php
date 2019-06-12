<?php


abstract class Shipment
{
    function listShippment(){

    }
    abstract function getShipment($id);
    abstract function createShipment($data);
    abstract function deleteShipment($id);


}