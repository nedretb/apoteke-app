<?php

class PismenoUpdate extends MainModel {
    protected $_mainClass, $db;
    protected static $new_table = 'users';

    function to_xml(SimpleXMLElement $object, array $data){
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $new_object = $object->addChild($key);
                to_xml($new_object, $value);
            } else {
                // if the key is an integer, it needs text with it to actually work.
                if ($key != 0 && $key == (int) $key) {
                    $key = "key_$key";
                }

                $object->addChild($key, $value);
            }
        }
    }


    public function returnVal($val = true){
        echo json_encode([
            'status' => $val
        ]);
    }

    public function log($method_name, $data){
        $filename = 'log.txt';
        $handle = fopen($filename, 'a+');
        fwrite($handle, date("l dS of F Y h:i:s A").' - '.$_SERVER['REMOTE_ADDR']."\r\n".$method_name."\r\n".print_r($data,true));
        fclose($handle);
    }

    public function PromjeniStatusPismena($userName, $jop, $status, $napomena){
        $ws_map = array();

        $ws_map['Person'] = $userName;
        $ws_map['Animal'] = $jop;

        foreach($ws_map as $soap_name => $php_name)
        {
            if($php_name === NULL)
            {
                //Map un-mapped SoapObjects to PHP classes
                $ws_map[$soap_name] = "MY_" . ucfirst($soap_name);
            }
        }

        return $ws_map;


        $this->log('PromjeniStatusPismena', ['username' => $userName, 'jop' => $jop, 'status' => $status, 'napomena' => $napomena]);

        $xml = new SimpleXMLElement('<rootTag/>');
        $this->to_xml($xml, [
            'PromjeniStatusPismenaResponse' => true
        ]);
        return $xml;
    }
}