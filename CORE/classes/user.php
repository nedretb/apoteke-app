<?php

class User{
    protected $pdo, $query;
    protected $role;          // 1. HR Admin 2. Ruk sektora 3. Ruk odjela 4. Ruk grupe 5. Ruk tima 6. User
    protected $children, $children_string;

    public function __construct($db){
        $this->pdo = $db;
    }
    public function allUsers(){
        return $this->query = $this->pdo->query("SELECT * from [c0_intranet2_apoteke].[dbo].[users]");
    }
    public function getUser($id){
        try{
            return $this->query = $this->pdo->query("SELECT * from [c0_intranet2_apoteke].[dbo].[users] where user_id = ".$id)->fetchAll();
        }catch (PDOException $e){
            return $e->getMessage();
        }
    }

    public function getAdmins(){
        $users = $this->pdo->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[users] where role = 4")->fetchAll();
        $id_s = '';
        for($i=0; $i<count($users); $i++){
            $id_s .= $users[$i]['user_id'];
            if($i != count($users) - 1) $id_s .= ', ';
        }
        return $id_s;
    }

    public function getParentOrImpersonator($id){
        $id_s = $this->getAdmins();
        try{
            $user = $this->pdo->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[users] where user_id = ".  $id)->fetch();

            $parent = $this->pdo->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[users] where employee_no = ".  $user['parent'])->fetch();
            $impersonator = $this->pdo->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[pm_impersonacija] where user_id = ".  $parent['user_id'])->fetchAll();
            if(count($impersonator)){
                if($id_s != ''){
                    $id_s .= ', '.$impersonator[0]['impersonator_id'].', '.$id;
                }else $id_s .= $impersonator[0]['impersonator_id'].', '.$id;
            }else {
                if($id_s != ''){
                    $id_s .= ', '.$parent['user_id'].', '.$id;
                }else $id_s .= $parent['user_id'].', '.$id;
            }

            return $id_s;
        }catch (PDOException $e){var_dump($e);}
    }

    public function getRole(){
        // Ovdje trebamo na neki način doći do toga da li je neko rukovodioc ili ne, i ako jeste čega je rukovodioc!
    }
    public function allEmployees(){
        // Ako je korisnik rukovodioc, onda daj sve uposlenika kojima je on nadležan
    }

    public function excapeChildren($array){
        return $condition = implode(', ', $array);
    }
    public function myImplode($char, $array){
        foreach($array as $elem){
            $this->children_string .= $elem['user_id'];

            if($elem['user_id'] != $array[count($array) - 1]['user_id']){
                $this->children_string .= $char;
            }
        }
        return $this->children_string;
    }
    public function getSQLChildren($id){
        try{
            $this->query = $this->pdo->query("SELECT user_id from [c0_intranet2_apoteke].[dbo].[users] where parent = '$id' or parentMBO2 = '$id' or parentMBO3 = '$id' or parentMBO4 = '$id' or parentMBO5 = '$id' ")->fetchAll();
            return $this->myImplode(', ', $this->query);
        }catch (PDOException $e){}
    }

    public function getSQLChildrenObjects($id, $user_id){ // $id - employee ID
        try{
            $in_values = $this->getSQLChildren($id);

            $impersonatori = $this->pdo->query("SELECT user_id FROM [c0_intranet2_apoteke].[dbo].[pm_impersonacija] where impersonator_id = ".$user_id)->fetchAll();
            foreach ($impersonatori as $impersonator){
                if(strlen($in_values)) $in_values .= ', ';
                $user = $this->pdo->query("SELECT employee_no FROM [c0_intranet2_apoteke].[dbo].[users] where user_id = ".$impersonator['user_id'])->fetch();
                $in_values .= $this->getSQLChildren($user['employee_no']);
            }

            return $this->pdo->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[users] WHERE user_id IN (".$in_values.")")->fetchAll();
        }catch (PDOException $e){ }
    }

    public function getAllChildrenFromUser($id, $user_id){ // $id - employee_no || $user_id - user_id
        $in_values = array();
        try{
            $this->query = $this->pdo->query("SELECT user_id from [c0_intranet2_apoteke].[dbo].[users] where parent = '$id' or parentMBO2 = '$id' or parentMBO3 = '$id' or parentMBO4 = '$id' or parentMBO5 = '$id' ")->fetchAll();
            //$in_values = $this->myImplode(', ', $this->query);
            foreach($this->query as $row){
                array_push($in_values, $row['user_id']);
            }
//            array_push($in_values, $this->query);

            $impersonatori = $this->pdo->query("SELECT user_id FROM [c0_intranet2_apoteke].[dbo].[pm_impersonacija] where impersonator_id = ".$user_id)->fetchAll();
            foreach ($impersonatori as $impersonator){
                //if(strlen($in_values)) $in_values .= ', ';
                $user = $this->pdo->query("SELECT employee_no FROM [c0_intranet2_apoteke].[dbo].[users] where user_id = ".$impersonator['user_id'])->fetch();

                $id = $user['employee_no'];
                $this->query = $this->pdo->query("SELECT user_id from [c0_intranet2_apoteke].[dbo].[users] where parent = '$id' or parentMBO2 = '$id' or parentMBO3 = '$id' or parentMBO4 = '$id' or parentMBO5 = '$id' ")->fetchAll();
                foreach($this->query as $row){
                    array_push($in_values, $row['user_id']);
                }
                //array_push($in_values, $this->query);
                //$in_values .= $this->getSQLChildren($user['employee_no']);
            }

            return $in_values;
        }catch (PDOException $e){return array();}
    }

    public function getChildren($id){
        try{
            return $this->query = $this->pdo->query("SELECT user_id from [c0_intranet2_apoteke].[dbo].[users] where parent = '$id' or parentMBO2 = '$id' or parentMBO3 = '$id' or parentMBO4 = '$id' or parentMBO5 = '$id' ")->fetchAll();
        }catch (PDOException $e){}
    }
}