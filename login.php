<?php
$host = '10.0.8.100';
$ldap_con = ldap_connect($host);
ldap_set_option($ldap_con, LDAP_OPT_PROTOCOL_VERSION, 3);

$ldap_username = 'epinsatest@mkt.gov.ba';
$ldap_password = 'Sarajevo101';

$uname = str_replace('@mbdom.rbbh', '', strtolower($ldap_username));


try{
    if(@ldap_bind($ldap_con,$ldap_username,$ldap_password)) echo "Success";
    else echo "Fail !";
}catch (\Exception $e){
    var_dump($e->getMessage());
}