<?php

// ------------------------------------------------------------------------------------------------------------------ //
// Metoda za ažuriraj pismeno

$response = 1;

if(isset($_POST['pismeno_jop'])){
    $jop      = $_POST['pismeno_jop'];
    $status   = (int)$_POST['pismeno_status'];
    $napomena = $_POST['pismeno_napomena'];

    try{
        $db->query("UPDATE [c0_intranet2_apoteke].[dbo].[plan_go_pismena] SET status = '{$status}', napomena = '{$napomena}' WHERE jop LIKE '$jop%'");
    }catch (\Exception $e){ $response = 0; }
    echo json_encode([
        'status' => $response
    ]);
}

// ------------------------------------------------------------------------------------------------------------------ //
// Metoda za ažuriranje korisnika

if(isset($_POST['kor_a_empno']) and isset($_POST['kor_a_ujed'])){
    $kor_a_empno  = $_POST['kor_a_empno'];
    $kor_a_ujed   = $_POST['kor_a_ujed'];
    $kor_a_rm     = $_POST['kor_a_rm'];
    $kor_a_dki    = $_POST['kor_a_dki'];
    $kor_a_domain = $_POST['kor_a_domain'];
    $kor_a_jez    = $_POST['kor_a_jez'];

    try{
        $db->query("UPDATE [c0_intranet2_apoteke].[dbo].[users] SET 
            egop_ustrojstvena_jedinica = '{$kor_a_ujed}',
            egop_radno_mjesto = '{$kor_a_rm}',
            egop_domensko_ki = '{$kor_a_dki}',
            egop_domena = '{$kor_a_domain}',
            egop_jezik = '{$kor_a_jez}'
            WHERE employee_no = '$kor_a_empno'");
    }catch (\Exception $e){ $response = $e; }
    echo json_encode([
        'status' => $response
    ]);
}