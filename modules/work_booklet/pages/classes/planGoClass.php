<?php
class planGo{
    // status -> NULL : Čekam na odgovor, 0 : Odbijen, 1 : Prihvaćen
    protected $_db;
    protected static $_curl;

    public function __construct(){

    }
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
    public static function getPlans($db){
        return $db->query("select * from [c0_intranet2_apoteke].[dbo].[predmeti] where vrsta_predmeta = 589")->fetchAll();
    }
    public static function getPismeno($id){
        return $db->query("select * from [c0_intranet2_apoteke].[dbo].[predmeti__pismena] where rbr_predmeta = ".$id)->fetchAll();
    }
    public static function createPlan(){
        try{
            $_user = _user(_decrypt($_SESSION['SESSION_USER']));
            $plan = self::_curlPost('http://localhost/apoteke-app/modules/default/pages/popup_plan_go.php', "_user=".$_user['employee_no'], true);

            return $plan->fileName;
        }catch (\Exception $e){
            return false;
        }
    }

    // -------------------------------------------------------------------------------------------------------------- //

    function array_to_xml( $data, &$xml_data ) {
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

    public function azurirajPismeno($jop, $status, $napomena){
        $response = true;

        try{
            $plan = self::_curlPost('http://localhost/apoteke-app/CORE/API/api-endpoint.php', "pismeno_jop=".$jop.'&pismeno_status='.$status.'&pismeno_napomena='.$napomena, true);

            $response = $plan->status;
        }catch (\Exception $e){ $response = false; }

        $array = [
            'jop' => $jop,
            'odgovor' => $response
        ];

        $xml_data = new SimpleXMLElement('<?xml version="1.0"?><data></data>');

        $this->array_to_xml($array,$xml_data);
        return $xml_data;
    }
}