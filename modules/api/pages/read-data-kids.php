<?php
include '../../../configuration.php';
include $root . '/classes/API/SoapEvents.php';
include 'func.php';

if (!class_exists('SoapClient')) {
    die ("You haven't installed the PHP-Soap module.");
}
use SoapEvents as Soap;


$data = Soap::getData('http://172.16.10.38:5203/PRH_WS/WS/JU%20Apoteke%20Sarajevo/Page/PRHWS_EmployeeRelative', 'ReadMultiple',
    ['filter' => ['Field' => 'Employee_No', 'Criteria' => 424], 'setSize' => '']);

var_dump($data->ReadMultiple_Result);
var_dump($data);