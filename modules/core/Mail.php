<?php

// Mail notifikacija

$get_mail_settings = $db->query("SELECT name, value FROM  ".$portal_settings."  WHERE name LIKE '%hr%mail' or name = 'hr_notifications'");
$get_mail_fetch = $get_mail_settings->fetchAll();

$mail_settings = array();
foreach ($get_mail_fetch as $key => $value) {
    $mail_settings[$value['name']] = $value['value'];
}

$array_bolovanje = array("43", "44", "45", "61", "62", "65", "67", "68", "69", "72", "73", "74", "75", "76", "77", "78", "81", "107", "108", "79", "80", "28", "29", "30", "31", "32", "27", "105", "106", "18", "19");

// Bolovanje i placena odsustva
if ($mail_settings['hr_notifications'] == '1') {
    if (in_array($_POST['status'], $array_bolovanje)) {
// start mail

        $status_izostanka = $status;

        $_user = _user(_decrypt($_SESSION['SESSION_USER']));
        if(isset($user_edit)){
            $_user = _employee($user_edit);
        }



        $user_edit = $_user;

        require '../../../lib/PHPMailer/PHPMailer.php';
        require '../../../lib/PHPMailer/SMTP.php';
        require '../../../lib/PHPMailer/Exception.php';
        require '../../../mails.php';

        $mail = new PHPMailer\PHPMailer\PHPMailer();
        $mail->CharSet = "UTF-8";

        $mail->IsSMTP();
        $mail->isHTML(true);  // Set email format to HTML




        $mail->Host = "barbbcom";
        //$mail->SMTPSecure = 'tls';
        $mail->Port = 25;

        $parent_user = _employee($_user['parent']);


        if (in_array($_POST['status'], array(73))) {
// sluzbeni put svi

            $mail->setFrom($_user['email_company'], "Obavijesti HR");

 $mail->addAddress("sluzbeni-put-rbbh@raiffeisengroup.ba");
//$mail->addAddress("raiffeisen_assistance@raiffeisengroup.ba");

        } else if (in_array($_POST['status'], array(81))) {
// sluzbeni put EDUKACIJA

            $mail->setFrom($_user['email_company'], "Obavijesti HR");

$mail->addAddress("edukacija.hr@raiffeisengroup.ba");


        } else if (in_array($_POST['status'], array(106, 18, 19))) {
// godišnji odmori

            $mail->setFrom($_user['email_company'], $_user['fname'] . ' ' . $_user['lname']);
$mail->addAddress($_user['email_company']);
$mail->addAddress($parent_user['email_company']); // todo nadredjeni mail

        } else {

            $mail->setFrom($_user['email_company'], $_user['fname'] . ' ' . $_user['lname']);
$mail->addAddress($_user['email_company']);
 $mail->addAddress(@$mail_settings['hr_supportt_mail']);
$mail->addAddress($parent_user['email_company']); // todo nadredjeni mail
        }

        $mail->Subject = 'Registracija izostanka';
//$_user=$user_edit;


        $mail->Body = $mails['day-edit'];




        if (!$mail->send()) {
//echo 'Message was not sent.';
//echo 'Mailer error: ' . $mail->ErrorInfo;
        } else {
//echo 'Message has been sent.';
        } 

      
    }
}