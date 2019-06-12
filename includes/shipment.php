<?php


abstract class Shipment
{
    function list_shipment(){

    }
    abstract function get_shipment($id);
    abstract function create_shipment($data);
    abstract function delete_shipment($id);


}