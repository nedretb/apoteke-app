<?php


class PlanGOAPI{
    public function PromjeniStatusPismena($userName, $jop, $status, $napomena){
        return json_encode([
            'status' => $val
        ]);
    }

//    public function log($method_name,$data){
//        $filename = 'classes/file.txt';
//        $handle = fopen($filename, 'a+');
//        fwrite($handle, date("l dS of F Y h:i:s A").' - '.$_SERVER['REMOTE_ADDR']."\r\n".$method_name."\r\n".print_r($data,true));
//        fclose($handle);
//    }
//
//    public function PromjeniStatusPismena($varr){
//        $this->log("Promijeni status", $varr);
//
//        return array("PromjeniStatusPismenaResult" => json_encode($varr), 'wee' => true);
//    }
}