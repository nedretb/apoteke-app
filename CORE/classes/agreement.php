<?php

class Agreement{
    protected $pdo, $userPDO, $query;
    protected $role;          // 1. HR Admin 2. Ruk sektora 3. Ruk odjela 4. Ruk grupe 5. Ruk tima 6. User
    protected $year;

    public function __construct($db){
        $this->pdo = $db;

        // Set year
        $this->year = date('Y');
    }
    public function formatDate($date){

        return $date;
        $dateTime = explode(" ", $date);
        $date = explode("-", $dateTime[0]);
        $date = $date[2].'.'.$date[1].'.'.$date[0].' ';
        $time = $dateTime[1][0].$dateTime[1][1].$dateTime[1][2].$dateTime[1][3].$dateTime[1][4];

        return $date.$time;
    }
    public function formatJustDate($date){
        if($date){
            $date = explode("-", $date);
            $date = $date[2].'.'.$date[1].'.'.$date[0].' ';
            return $date;
        }else return "";
    }

    public function getAgreementById($id){
        try{
            return $this->pdo->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[pm_sporazumi]  as s join [c0_intranet2_apoteke].[dbo].[users] as u on u.user_id = s.user_id where s.id = ".$id)->fetch();
        }catch (PDOException $e){}
    }

    public function insert_new($user_id){
        $this->pdo->query("INSERT INTO [c0_intranet2_apoteke].[dbo].[pm_sporazumi] (user_id, year, status) values ('$user_id', '$this->year', '0')");

        // Uzmi ID od novokreiranog sporazuma
        $new = $this->pdo->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[pm_sporazumi] where status = 0 and user_id = ".$user_id)->fetch();
        $sporazum_id = $new['id'];

        $kompetencije = $this->pdo->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[pm_kompetencija_lista]")->fetchAll();
        foreach($kompetencije as $kometencija){
            $kompetencija_id = $kometencija['id'];

            $this->pdo->query("INSERT INTO [c0_intranet2_apoteke].[dbo].[pm_sporazumi_kompetencije_rel] (sporazum_id, kompetencija_id, checked) values ('$sporazum_id', '$kompetencija_id', '1')");
        }
    }

    public function check_for_active_agreement($user_id){
        try{
            $number = count($this->pdo->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[pm_sporazumi] where status = 0 and user_id = ".$user_id)->fetchAll());
            if(!$number){
                $this->insert_new($user_id);
            }
            return $this->pdo->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[pm_sporazumi] where status = 0 and user_id = ".$user_id)->fetchAll()[0];
        }catch (PDOException $e){return $e->getMessage();}
    }
    public function getAgreement($user_id){
        return $this->pdo->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[pm_sporazumi] as s join [c0_intranet2_apoteke].[dbo].[users] as u on u.user_id = s.user_id where s.user_id = ".$user_id)->fetchAll();
    }
    public function getSentAgreement($user_id){
        $date   = date('Y-m-d');
        $edit   = count($this->pdo->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[pm_kalendar] where datum_od <= '$date' and datum_do >= '$date' and faza = 2 ")->fetchAll());
        $evolve = count($this->pdo->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[pm_kalendar] where datum_od <= '$date' and datum_do >= '$date' and faza = 3 ")->fetchAll());
        if($edit or $evolve){
            return $this->pdo->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[pm_sporazumi] as s join [c0_intranet2_apoteke].[dbo].[users] as u on u.user_id = s.user_id where s.user_id = ".$user_id)->fetchAll();
        }else{
            return $this->pdo->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[pm_sporazumi] as s join [c0_intranet2_apoteke].[dbo].[users] as u on u.user_id = s.user_id where s.sent = 1 and s.user_id = ".$user_id)->fetchAll();
        }
    }

    // Agreement - GOALS
    public function allGoals($id){
        try{
            //$this->pdo->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[pm_sporazumi_ciljevi] where id_sporazuma=".$id)->fetchAll();

            return $this->pdo->query("SELECT 
                        [c0_intranet2_apoteke].[dbo].[pm_sporazumi_ciljevi].*,
                        [c0_intranet2_apoteke].[dbo].[users].[fname],
                        [c0_intranet2_apoteke].[dbo].[users].[lname]
                        
                        FROM [c0_intranet2_apoteke].[dbo].[pm_sporazumi_ciljevi] 
                        LEFT JOIN [c0_intranet2_apoteke].[dbo].[users] ON [c0_intranet2_apoteke].[dbo].[pm_sporazumi_ciljevi].[disabled] = [c0_intranet2_apoteke].[dbo].[users].[user_id]
                        where id_sporazuma = ".$id." order by disabled DESC")->fetchAll();

        }catch (PDOException $e){return $e->getMessage();}
    }
    public function getGoal($id){
        try{
            return $this->pdo->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[pm_sporazumi_ciljevi] where id=".$id)->fetch();
        }catch (PDOException $e){return $e->getMessage();}
    }

    public function updateSent($id){
        try{
            // Ako nije unesen development plan, ne možeš poslati, izbaci grešku !
            $dev_plan = $this->pdo->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[pm_sporazumi] where id=".$id)->fetch();

            $goals = $this->pdo->query("SELECT tezina FROM [c0_intranet2_apoteke].[dbo].[pm_sporazumi_ciljevi] where id_sporazuma = '{$id}' and disabled IS NULL")->fetchAll();
            $total = 0;
            foreach($goals as $goal){
                $total += $goal['tezina'];
            }

            if($dev_plan['development_plan'] and $total == 100.00){
                $this->pdo->query("UPDATE [c0_intranet2_apoteke].[dbo].[pm_sporazumi] SET sent = 1 where id=".$id);
            }else{
                if(!$dev_plan['development_plan']) return $warning_message = 'Razvojni plan nije unesen !';
                return $warning_message = 'Greška. Ukupna težina ciljeva treba biti 100% te ';
            }
            // TODO - Send an email to supervisor
        }catch (PDOException $e){}
    }


    // Check if user is supervisor or impersonator for this agreement
    public function checkSupervisor($user_id, $agreement_id){
        // Prvo ćemo naći user_id od sporazuma


        $usr_id = $this->pdo->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[pm_sporazumi] where id = ".$agreement_id)->fetch()['user_id']; // ID od usera koji je kreirao sporazum
        $user   = $this->pdo->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[users] where user_id = ".$usr_id)->fetch(); // User koji je kreirao sporazum

        // Eh odobravati ili bilo kakve akcije mogu raditi samo prvi nadređeni ili njihovi impersonatori
        $parent_id = $user['parent'];
        $parent    = $this->pdo->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[users] where employee_no = ".$parent_id)->fetch();


        if($parent['user_id'] == $user_id) return true;

        // ako nije parent, onda možda jeste impersonator
        $impersonatori = $this->pdo->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[pm_impersonacija] where impersonator_id = ".$user_id)->fetchAll();
        foreach ($impersonatori as $impersonator){
            if($impersonator['user_id'] == $parent['user_id']) return true;
        }

        return false;
    }
    public function checkUserOwner($user_id, $agreement_id){
        $usr_id = $this->pdo->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[pm_sporazumi] where id = ".$agreement_id)->fetch()['user_id']; // ID od usera koji je kreirao sporazum
        if($user_id == $usr_id) return true;
        return false;
    }
}