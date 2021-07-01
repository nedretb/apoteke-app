<?php

class PM
{
    protected $pdo, $userPDO, $query;
    protected $role;          // 1. HR Admin 2. Ruk sektora 3. Ruk odjela 4. Ruk grupe 5. Ruk tima 6. User

    public function __construct($db)
    {
        $this->pdo = $db;
    }

    public function checkForImpersonator($id)
    {
        try {
            return count($this->pdo->query("SELECT * from  " . $portal_pm_impersonacija . "  where user_id = " . $id)->fetchAll());
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }

    public function getMyImpersonator($id)
    {
        $this->query = $this->pdo->query("SELECT * from  " . $portal_pm_impersonacija . "  where user_id = " . $id)->fetchAll(); // returns an array
        if (count($this->query)) {
            // Pronašli smo uzorak - sad nam treba korisnik sa tim ID-om
            $user = new User($this->pdo);

            return $user->getUser($this->query[0]['impersonator_id']);
        }
        return null;
    }

    public function updateImpersonator($user_id, $imp_id)
    {
        $this->query = count($this->pdo->query("SELECT * from  " . $portal_pm_impersonacija . "  where impersonator_id = " . $imp_id)->fetchAll()); // returns an array

        try {
            return $this->pdo->query("UPDATE  " . $portal_pm_impersonacija . "  SET impersonator_id = '$imp_id' where user_id = " . $user_id);
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }

    public function insertImpersonator($user_id, $imp_id)
    {
        $this->query = count($this->pdo->query("SELECT * from  " . $portal_pm_impersonacija . "  where impersonator_id = " . $imp_id)->fetchAll()); // returns an array

        try {
            return $this->pdo->query("INSERT INTO  " . $portal_pm_impersonacija . "  (impersonator_id, user_id) VALUES  ('$imp_id', '$user_id') ");
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }
}