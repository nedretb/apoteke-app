<?php

class MainClass{
    protected static $_curl;

    public static function _curlPost($uri, $parameters, $json = false){
        self::$_curl = curl_init();

        curl_setopt_array(
            self::$_curl, array(
            CURLOPT_URL => $uri,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $parameters
        ));

        $output = curl_exec(self::$_curl);
        if($json) return json_decode($output);
        else return $output;
    }

    public static function ArrToXML( $data, &$xml_data ) {
        foreach( $data as $key => $value ) {
            if( is_array($value) ) {
                if( is_numeric($key) ){
                    $key = 'item'.$key; //dealing with <0/>..<n/> issues
                }
                $subnode = $xml_data->addChild($key);
                array_to_xml($value, $subnode);
            } else {
                $xml_data->addChild("$key",htmlspecialchars("$value"));
            }
        }
    }

    public function execQuery($query){

    }
    public static function testData(){
        return "Testit !";
    }
}