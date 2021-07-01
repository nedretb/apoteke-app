<?php


class IzvozSatnica{
    protected static $syst = [], $months, $years;

    public static function dajSistematizacije($id){
        if($_user['role'] == 4){
            $syst = Sistematizacija::getSys();
        }else{
            $syst = Sistematizacija::getSys($_user);
        }
        foreach ($syst as $sys){
            self::$syst[$sys['id']] = $sys['title'];
        }

        return self::$syst;
    }
    public static function dajMjesece(){
        return ['1' => 'Januar', '2' => 'Februar', '3' => 'Mart', '4' => 'April', '5' => 'Maj', '6' => 'Juni', '7' => 'Juli', '8' => 'August', '9' => 'Septembar', '10' => 'Oktobar', '11' => 'Novembar', ' 12' => 'Decembar'];
    }
    public static function dajGodine(){
        for($i = 2018; $i<=date('Y'); $i++){
            self::$years[$i] = $i;
        }
        return self::$years;
    }
}