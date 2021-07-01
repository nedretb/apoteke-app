<?php
ini_set("soap.wsdl_cache_enabled", "0");

class Server{
    protected $class_name = '';
    public function __construct($class_name){
        $this->class_name = $class_name;
    }
    public function log($method_name,$data){
        $filename = 'log.txt';
        $handle = fopen($filename, 'a+');
        fwrite($handle, date("l dS of F Y h:i:s A").' - '.$_SERVER['REMOTE_ADDR']."\r\n".$method_name."\r\n".print_r($data,true));
        fclose($handle);
    }

    public function actionFun($params){
        $jop    = $params[0]->promjeniStatus->jop;
        $status = $params[0]->promjeniStatus->jop;
    }

    public function __call($method_name, $parameters){
        $this->log($method_name,$parameters[0]->promjeniStatus->jop); //  log

        $this->actionFun($parameters);

        if(!method_exists($this->class_name, $method_name )) return 'Method '.$method_name.' not found'; // methot exist check
        return call_user_func_array(array($this->class_name, $method_name ), $parameters); //call method
    }
}


class Pismeno {
    public function PromjeniStatusPismena ($parameters){
        $promjeniStatus = $parameters;
        return self::PromjeniStatusPismenaResponse($promjeniStatus);
    }

    public function PromjeniStatusPismenaResponse ($message){
        return [
            'PromjeniStatusPismenaResult' => [
                'OperationSucceeded' => 1,
                'Errors' => 'none'
            ]
        ];

        $ws_map['PromjeniStatusPismenaResult'] = ([
            'OperationSucceeded' => ucfirst(1),
            'Errors' => ucfirst('none')
        ]);
        foreach($ws_map as $soap_name => $php_name) {
            if($php_name === NULL)
            {
                //Map un-mapped SoapObjects to PHP classes
                $ws_map[$soap_name] = "MY_" . ucfirst($soap_name);
            }
        }
        return $ws_map;
    }
}

class in {

}

$Service = new Server('Pismeno');
$classmap=[
    'in' => 'in'
];
$server = new SOAPServer('classes/service.wsdl', array(
    'soap_version' => SOAP_1_2,
    'style' => SOAP_RPC,
    'use' => SOAP_LITERAL,
    'classmap'=>$classmap
));
$server->setObject($Service);
$server->handle();