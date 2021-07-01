<?php

class Korisnici extends MainModel {
    protected $_mainClass, $db;
    protected static $new_table = 'users';

    public function __construct(){
        $this->_mainClass = new MainClass();
        $this->db = $db;
    }
    public function returnVal($val = true){
        echo json_encode([
            'status' => $val
        ]);
    }
    public static function parseEN($employee_no){
        $number = 0;
        for($i=0; $i<strlen($employee_no); $i++){
            if(ord($employee_no[$i]) >= 48 and ord($employee_no[$i]) <= 57){ // it is a integer value
                $number *= 10;
                $number += $employee_no[$i];
            }
        }

        return $number;
    }

    public function azurirajKorisnika($employee_no, $ime, $prezime, $jmb, $nadredjeni, $rukovodioc, $uJedinica, $radnoMj, $domenKI, $domena, $jezik, $status){
        try{
            $employee_no = self::parseEN($employee_no);
            $nadredjeni  = self::parseEN($nadredjeni);

            self::where('employee_no = '.$employee_no)->update([
                'fname' => $ime,
                'lname' => $prezime,
                'username' => $domenKI,
                'parent' => $nadredjeni,
                'jmb' => $jmb,
                'rukovodioc' => $rukovodioc,
                'egop_ustrojstvena_jedinica' => $uJedinica,
                'egop_radno_mjesto' => $radnoMj,
                'egop_domena' => $domena,
                'egop_jezik' => $jezik,
                'aktivan' => $status,
            ]);
        }catch (\Exception $e){
            return $this->returnVal(false);
        }
        return $this->returnVal();
    }
    public function kreirajKorisnika($employee_no, $ime, $prezime, $jmb, $nadredjeni, $rukovodioc, $uJedinica, $radnoMj, $domenKI, $domena, $jezik, $status){
        try{
            $employee_no = self::parseEN($employee_no);
            $nadredjeni  = self::parseEN($nadredjeni);

            self::insert([
                'employee_no' => $employee_no,
                'fname' => $ime,
                'lname' => $prezime,
                'jmb' => $jmb,
                'parent' => $nadredjeni,
                'rukovodioc' => $rukovodioc,
                'egop_ustrojstvena_jedinica' => $uJedinica,
                'egop_radno_mjesto' => $radnoMj,
                'username' => $domenKI,
                'password' => md5(time()),
                'role' => 2,
                'email' => $domenKI,
                'lang' => 2,
                'status' => 1,
                'dates' => date('Y-m-d'),
                'dates_deactivate' => date('Y-m-d'),
                'dates_reactivate' => date('Y-m-d'),
                'egop_domena' => $domena,
                'egop_jezik' => $jezik,
                'aktivan' => $status,
            ]);
        }catch (\Exception $e){
            return $this->returnVal(false);
        }
        return $this->returnVal();
    }
}