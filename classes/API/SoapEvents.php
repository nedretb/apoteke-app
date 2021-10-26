<?php

class SoapEvents{
    protected static $_client, $_data;

    public function __construct($uri = null){

    }
    public static function getData($uri, $method, $param){
        try{
            $soapParameters = array('login' => 'PRH_WS', 'password' => "dZQ1jkEe8+VcZ3ll5fPuz2Mm8zrDmJP2KuohrbItOIg=") ;
            self::$_client = new SoapClient($uri, $soapParameters);

            return self::$_data = self::$_client->$method($param);
        }catch (\Exception $e){var_dump($e);}
    }

}