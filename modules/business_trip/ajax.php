<?php

require_once '../../configuration.php';

  if(DEBUG){

    error_reporting(E_ALL);
    ini_set('display_errors', 1);

  }

if(isset($_POST['request'])){


  
   if($_POST['request']=='business-trip-request-response'){

     $_user = _user(_decrypt($_SESSION['SESSION_USER']));

     $status_get = $_POST['status'];


$get = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[business_trip] WHERE request_id='".$_POST['request_id']."'");
       if($get->rowCount()<0){
         $row = $get->fetch();
         $user = _user($row['user_id']);


      $query = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[business_trip] WHERE request_id='".$_POST['request_id']."'");
      $get2 = $db->query("SELECT COUNT(*) FROM [c0_intranet2_apoteke].[dbo].[business_trip]");
      $total = $get2->rowCount();
      foreach($query as $item){
         $tools_id = $item['request_id'];
         $request_parent=$item['parent'];
         $request_parent2=$item['parent2'];
         $request_stream = $item['hr'];
         $request_admin=$item['admin'];
         $request_admin2=$item['admin2'];
         $request_user=$item['user_id'];
         $get_country=$item['country_ino'];
      }
    }





    $stream_query  = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[users] WHERE employee_no='".$request_stream."'");

    foreach($stream_query as $uquery) {
    
    $email_stream_parent = $uquery['email'];
  
    }


    $admin_query  = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[users] WHERE employee_no='".$request_admin."'");

    foreach($admin_query as $uquery) {
    
    $email_admin = $uquery['email'];
  
    }
  
    $admin2_query  = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[users] WHERE employee_no='".$request_admin2."'");

    foreach($admin2_query as $uquery) {
    
    $email_admin2 = $uquery['email'];
  
    }

    $user_query  = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[users] WHERE employee_no='".$request_user."'");

    foreach($user_query as $uquery) {
    
    $email_user = $uquery['email'];
  
    }


 
    date_default_timezone_set('Europe/Sarajevo');
     //echo date('Y-m-d H:i:s T', time()) . "<br>\n";

    $data = "UPDATE [c0_intranet2_apoteke].[dbo].[business_trip] SET
      status = ?,
      date_response = ?
      WHERE request_id = ?";

    $res = $db->prepare($data);
    $res->execute(
      array(
        $_POST['status'],
        date('Y-m-d H:i:s'),
        $_POST['request_id']
      )
    );
    if($res->rowCount()==1) {
      echo '<div class="alert alert-success text-center">'.__('Informacije su uspješno spašene!').'</div>';

    }

/*if($get_country==1) {
if($status_get==2){
require '../../send-email-from-localhost-php/PHPMailer/PHPMailerAutoload.php';

$mail = new PHPMailer(true);
$mail->CharSet = "UTF-8";


$mail->isSMTP(); 
//$mail->Host = '91.235.170.162';   
$mail->Host = gethostbyname('mail.teneo.ba');                   // Specify main and backup SMTP servers
$mail->SMTPAuth = true;                            // Enable SMTP authentication
$mail->Username = 'nav@teneo.ba';          // SMTP username
$mail->Password = 'DynamicsNAV16!'; // SMTP password

$mail->Port = 587;  


$mail->SMTPSecure = false;
$mail->SMTPAutoTLS = false;                              // TCP port to connect to

$mail->setFrom('nav@teneo.ba', 'Employee Portal Info');
//$mail->addReplyTo('irma.hrelja@infodom.ba', 'Irma');
//$mail->addAddress($email);  
 //$mail->addAddress('irma.hrelja@infodom.ba'); 
    //$mail->addAddress($email);
    //$mail->addAddress($email_parent2);
    //$mail->addAddress($email_user);
    //echo $email;
    //echo $email_parent2;
    //echo $email_user;


 // Add a recipient
//$mail->addCC('nermina.kraljic@infodom.ba');
//$mail->addBCC('bcc@example.com');
    $mail->addAddress('nav@teneo.ba'); 

$mail->isHTML(true);  // Set email format to HTML
$bodyContent = '<h5 style="color:gray;font-weight:normal;">Poštovana/i, </h5 >' ;

$bodyContent .= '<h5 style="color:gray;font-weight:normal;">Ovim putem želimo Vas obavijestiti da je za zaposlenika'.' '.$user['fname'].' '.$user['lname'].' odbijen Zahtjev za Službeni put u periodu od '.date('d/m/Y',strtotime($row['h_from'])).' do '.date('d/m/Y',strtotime($row['h_to'])).'. </h5 >' ;
$bodyContent .= '<a href=http://infodom-server.sarajevo-infodom.ba:81/app/?m=business_trip2&p=popup_business_trip_add_view_parent&id='.$tools_id.'>Za direktan pristup pregleda Zahtjeva, kliknite ovdje.</a>';
$bodyContent .= '<h5 style="color:gray;font-weight:normal;">S poštovanjem, <br> Vaš HR Tim </h5 >' ;
$mail->Subject = 'Employee Portal!';
$mail->Body    = $bodyContent;

    
if(!$mail->send()) {
   echo 'Message could not be sent.';
   echo 'Mailer Error: ' . $mail->ErrorInfo;
} else { ?>
      

<?php }

    

  }


 else if($status_get==1){
require '../../send-email-from-localhost-php/PHPMailer/PHPMailerAutoload.php';

$mail = new PHPMailer(true);
$mail->CharSet = "UTF-8";


$mail->isSMTP(); 
//$mail->Host = '91.235.170.162';   
$mail->Host = gethostbyname('mail.teneo.ba');                   // Specify main and backup SMTP servers
$mail->SMTPAuth = true;                            // Enable SMTP authentication
$mail->Username = 'nav@teneo.ba';          // SMTP username
$mail->Password = 'DynamicsNAV16!'; // SMTP password

$mail->Port = 587;  


$mail->SMTPSecure = false;
$mail->SMTPAutoTLS = false;                              // TCP port to connect to

$mail->setFrom('nav@teneo.ba', 'Employee Portal Info');
//$mail->addReplyTo('irma.hrelja@infodom.ba', 'Irma');
//$mail->addAddress($email);  
 //$mail->addAddress('irma.hrelja@infodom.ba');
    //$mail->addAddress( $email);
    //$mail->addAddress($email_parent2);
    //$mail->addAddress($email_user);
    $mail->addAddress('nav@teneo.ba'); 
    //echo $email;
    //echo $email_parent2;
    //echo $email_user;
 // Add a recipient
//$mail->addCC('nermina.kraljic@infodom.ba');
//$mail->addBCC('bcc@example.com');

$mail->isHTML(true);  // Set email format to HTML
$bodyContent = '<h5 style="color:gray;font-weight:normal;">Poštovana/i, </h5 >' ;

$bodyContent .= '<h5 style="color:gray;font-weight:normal;">Ovim putem želimo Vas obavijestiti da je za zaposlenika'.' '.$user['fname'].' '.$user['lname'].' odobren Službeni put u periodu od '.date('d/m/Y',strtotime($row['h_from'])).' do '.date('d/m/Y',strtotime($row['h_to'])).'. </h5 >' ;
$bodyContent .= '<a href=http://infodom-server.sarajevo-infodom.ba:81/app/?m=business_trip&p=popup_business_trip2_reponse&id='.$tools_id.'>Za direktan pristup pregleda Zahtjeva, kliknite ovdje.</a>';
$bodyContent .= '<h5 style="color:gray;font-weight:normal;">S poštovanjem, <br> Vaš HR Tim </h5 >' ;
$mail->Subject = 'Employee Portal!';
$mail->Body    = $bodyContent;

    
if(!$mail->send()) {
   echo 'Message could not be sent.';
   echo 'Mailer Error: ' . $mail->ErrorInfo;
} else { ?>
      

<?php }

    

  }
}*/


/*******************INO SATNICE MAIL***********************************/

if($get_country!=1) {
if($status_get==2){
require '../../send-email-from-localhost-php/PHPMailer/PHPMailerAutoload.php';

$mail = new PHPMailer(true);
$mail->CharSet = "UTF-8";


$mail->isSMTP(); 
//$mail->Host = '91.235.170.162';   
$mail->Host = gethostbyname('mail.teneo.ba');                   // Specify main and backup SMTP servers
$mail->SMTPAuth = true;                            // Enable SMTP authentication
$mail->Username = 'nav@teneo.ba';          // SMTP username
$mail->Password = 'DynamicsNAV16!'; // SMTP password

$mail->Port = 587;  


$mail->SMTPSecure = false;
$mail->SMTPAutoTLS = false;                              // TCP port to connect to

$mail->setFrom('nav@teneo.ba', 'Employee Portal Info');


    //$mail->addAddress($email_admin);
    //$mail->addAddress($email_admin2);
    //$mail->addAddress($email_user);
    //$mail-<addAddress($email_stream_parent);

    $mail->addAddress('nav@teneo.ba'); 
 // Add a recipient

//$mail->addBCC('bcc@example.com');

$mail->isHTML(true);  // Set email format to HTML
$bodyContent = '<h5 style="color:gray;font-weight:normal;">Poštovana/i, </h5 >' ;

$bodyContent .= '<h5 style="color:gray;font-weight:normal;">Ovim putem želimo Vas obavijestiti da je za zaposlenika'.' '.$user['fname'].' '.$user['lname'].' odbijen Zahtjev za Službeni put u periodu od '.date('d/m/Y',strtotime($row['h_from'])).' do '.date('d/m/Y',strtotime($row['h_to'])).'. </h5 >' ;
$bodyContent .= '<a href=http://infodom-server.sarajevo-infodom.ba:81/app/?m=business_trip2&p=popup_business_trip_add_view_parent&id='.$tools_id.'>Za direktan pristup pregleda Zahtjeva, kliknite ovdje.</a>';
$bodyContent .= '<h5 style="color:gray;font-weight:normal;">S poštovanjem, <br> Vaš HR Tim </h5 >' ;
$mail->Subject = 'Employee Portal!';
$mail->Body    = $bodyContent;

    
if(!$mail->send()) {
   echo 'Message could not be sent.';
   echo 'Mailer Error: ' . $mail->ErrorInfo;
} else { ?>
      

<?php }

    

  }


 else if($status_get==1){
require '../../send-email-from-localhost-php/PHPMailer/PHPMailerAutoload.php';

$mail = new PHPMailer(true);
$mail->CharSet = "UTF-8";


$mail->isSMTP(); 
//$mail->Host = '91.235.170.162';   
$mail->Host = gethostbyname('mail.teneo.ba');                   // Specify main and backup SMTP servers
$mail->SMTPAuth = true;                            // Enable SMTP authentication
$mail->Username = 'nav@teneo.ba';          // SMTP username
$mail->Password = 'DynamicsNAV16!'; // SMTP password

$mail->Port = 587;  


$mail->SMTPSecure = false;
$mail->SMTPAutoTLS = false;                              // TCP port to connect to

$mail->setFrom('nav@teneo.ba', 'Employee Portal Info');
$mail->addAddress('nav@teneo.ba'); 
   
    //$mail->addAddress($email_admin);
    //$mail->addAddress($email_admin2);
    //$mail-<addAddress($email_stream_parent);


 // Add a recipient
//$mail->addCC('nermina.kraljic@infodom.ba');
//$mail->addBCC('bcc@example.com');

$mail->isHTML(true);  // Set email format to HTML
$bodyContent = '<h5 style="color:gray;font-weight:normal;">Poštovana/i, </h5 >' ;

$bodyContent .= '<h5 style="color:gray;font-weight:normal;">Ovim putem želimo Vas obavijestiti da je za zaposlenika'.' '.$user['fname'].' '.$user['lname'].' odobren Službeni put u periodu od '.date('d/m/Y',strtotime($row['h_from'])).' do '.date('d/m/Y',strtotime($row['h_to'])).'. </h5 >' ;
$bodyContent .= '<a href=http://infodom-server.sarajevo-infodom.ba:81/app/?m=business_trip&p=popup_business_trip2_reponse&id='.$tools_id.'>Za direktan pristup odobravanju Zahtjeva, kliknite ovdje.</a>';
$bodyContent .= '<h5 style="color:gray;font-weight:normal;">S poštovanjem, <br> Vaš HR Tim </h5 >' ;
$mail->Subject = 'Employee Portal!';
$mail->Body    = $bodyContent;

    
if(!$mail->send()) {
   echo 'Message could not be sent.';
   echo 'Mailer Error: ' . $mail->ErrorInfo;
} else { ?>
      

<?php }

    

  }
}





}




/******************************** ADMIN RESPONSE *****************************************************/

if($_POST['request']=='business-trip-request-response-admin'){
     $_user = _user(_decrypt($_SESSION['SESSION_USER']));

     $status_get = $_POST['status_admin'];


$get = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[business_trip] WHERE request_id='".$_POST['request_id']."'");
       if($get->rowCount()<0){
         $row = $get->fetch();
         $user = _user($row['user_id']);


      $query = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[business_trip] WHERE request_id='".$_POST['request_id']."'");
      $get2 = $db->query("SELECT COUNT(*) FROM [c0_intranet2_apoteke].[dbo].[business_trip]");
      $total = $get2->rowCount();
      foreach($query as $item){
         $tools_id = $item['request_id'];
         $request_parent=$item['parent'];
         $request_parent2=$item['parent2'];
         $request_stream = $item['hr'];
         $request_admin=$item['admin'];
         $request_admin2=$item['admin2'];
         $request_user=$item['user_id'];
         $get_country=$item['country_ino'];
      }
    }


   
    $stream_query  = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[users] WHERE employee_no='".$request_stream."'");

    foreach($stream_query as $uquery) {
    
    $email_stream_parent = $uquery['email'];
  
    }


    $parent_query  = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[users] WHERE employee_no='".$request_parent."'");

    foreach($parent_query as $uquery) {
    
    $email_parent = $uquery['email'];
  
    }


    $admin_query  = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[users] WHERE employee_no='".$request_admin."'");

    foreach($admin_query as $uquery) {
    
    $email_admin = $uquery['email'];
  
    }
  
    $admin2_query  = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[users] WHERE employee_no='".$request_admin2."'");

    foreach($admin2_query as $uquery) {
    
    $email_admin2 = $uquery['email'];
  
    }

    $user_query  = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[users] WHERE employee_no='".$request_user."'");

    foreach($user_query as $uquery) {
    
    $email_user = $uquery['email'];
  
    }
       
    date_default_timezone_set('Europe/Sarajevo');

    $data = "UPDATE [c0_intranet2_apoteke].[dbo].[business_trip] SET
      status_admin_response = ?,
      date_status_response_admin = ?
      WHERE request_id = ?";

    $res = $db->prepare($data);
    $res->execute(
      array(
        $_POST['status_admin'],
        date('Y-m-d H:i:s'),
        $_POST['request_id']
      )
    );
    if($res->rowCount()==1) {
    

      echo '<div class="alert alert-success text-center">'.__('Informacije su uspješno spašene!').'</div>';
    }

 if($get_country!=1) {
if($status_get==2){
require '../../send-email-from-localhost-php/PHPMailer/PHPMailerAutoload.php';

$mail = new PHPMailer(true);
$mail->CharSet = "UTF-8";


$mail->isSMTP(); 
//$mail->Host = '91.235.170.162';   
$mail->Host = gethostbyname('mail.teneo.ba');                   // Specify main and backup SMTP servers
$mail->SMTPAuth = true;                            // Enable SMTP authentication
$mail->Username = 'nav@teneo.ba';          // SMTP username
$mail->Password = 'DynamicsNAV16!'; // SMTP password

$mail->Port = 587;  


$mail->SMTPSecure = false;
$mail->SMTPAutoTLS = false;                              // TCP port to connect to

$mail->setFrom('nav@teneo.ba', 'Employee Portal Info');

    //$mail->addAddress($email_admin2);
    //$mail->addAddress($email_user);
    //$mail-<addAddress($email_parent);
    //$mail-<addAddress($email_stream_parent);

$mail->addAddress('nav@teneo.ba'); 
 // Add a recipient
//$mail->addBCC('bcc@example.com');

$mail->isHTML(true);  // Set email format to HTML
$bodyContent = '<h5 style="color:gray;font-weight:normal;">Poštovana/i, </h5 >' ;

$bodyContent .= '<h5 style="color:gray;font-weight:normal;">Ovim putem želimo Vas obavijestiti da je za zaposlenika'.' '.$user['fname'].' '.$user['lname'].' odbijen Zahtjev za Službeni put u periodu od '.date('d/m/Y',strtotime($row['h_from'])).' do '.date('d/m/Y',strtotime($row['h_to'])).'. </h5 >' ;
$bodyContent .= '<h5 style="color:gray;font-weight:normal;">Za više informacija, molimo Vas kontaktirajte kolegice Almiru Sadiković i/ili Lamiju Alajbegović. </h5 >' ;
$bodyContent .= '<h5 style="color:gray;font-weight:normal;">S poštovanjem, <br> Vaš HR Tim </h5 >' ;
$mail->Subject = 'Employee Portal!';
$mail->Body    = $bodyContent;


    
if(!$mail->send()) {
   echo 'Message could not be sent.';
   echo 'Mailer Error: ' . $mail->ErrorInfo;
} else { ?>
      

<?php }

    

  }

 else if($status_get==1){
require '../../send-email-from-localhost-php/PHPMailer/PHPMailerAutoload.php';

$mail = new PHPMailer(true);
$mail->CharSet = "UTF-8";


$mail->isSMTP(); 
//$mail->Host = '91.235.170.162';   
$mail->Host = gethostbyname('mail.teneo.ba');                   // Specify main and backup SMTP servers
$mail->SMTPAuth = true;                            // Enable SMTP authentication
$mail->Username = 'nav@teneo.ba';          // SMTP username
$mail->Password = 'DynamicsNAV16!'; // SMTP password

$mail->Port = 587;  


$mail->SMTPSecure = false;
$mail->SMTPAutoTLS = false;                              // TCP port to connect to

$mail->setFrom('nav@teneo.ba', 'Employee Portal Info');
    //$mail->addAddress($email_admin2);
    //$mail->addAddress($email_user);
    //$mail-<addAddress($email_parent);
    //$mail-<addAddress($email_stream_parent);
$mail->addAddress('nav@teneo.ba'); 
 // Add a recipient

//$mail->addBCC('bcc@example.com');

$mail->isHTML(true);  // Set email format to HTML
$bodyContent = '<h5 style="color:gray;font-weight:normal;">Poštovana/i, </h5 >' ;

$bodyContent .= '<h5 style="color:gray;font-weight:normal;">Ovim putem želimo Vas obavijestiti da je za zaposlenika'.' '.$user['fname'].' '.$user['lname'].' odobren Zahtjev za Službeni put u periodu od '.date('d/m/Y',strtotime($row['h_from'])).' do '.date('d/m/Y',strtotime($row['h_to'])).'. </h5 >' ;
$bodyContent .= '<h5 style="color:gray;font-weight:normal;">Za više informacija, molimo Vas kontaktirajte kolegice Almiru Sadiković i/ili Lamiju Alajbegović. </h5 >' ;
$bodyContent .= '<h5 style="color:gray;font-weight:normal;">S poštovanjem, <br> Vaš HR Tim </h5 >' ;
$mail->Subject = 'Employee Portal!';
$mail->Body    = $bodyContent;


    
if(!$mail->send()) {
   echo 'Message could not be sent.';
   echo 'Mailer Error: ' . $mail->ErrorInfo;
} else { ?>
      

<?php }

    

  }
}


if($get_country==1) {
if($status_get==2){
require '../../send-email-from-localhost-php/PHPMailer/PHPMailerAutoload.php';

$mail = new PHPMailer(true);
$mail->CharSet = "UTF-8";


$mail->isSMTP(); 
//$mail->Host = '91.235.170.162';   
$mail->Host = gethostbyname('mail.teneo.ba');                   // Specify main and backup SMTP servers
$mail->SMTPAuth = true;                            // Enable SMTP authentication
$mail->Username = 'nav@teneo.ba';          // SMTP username
$mail->Password = 'DynamicsNAV16!'; // SMTP password

$mail->Port = 587;  


$mail->SMTPSecure = false;
$mail->SMTPAutoTLS = false;                              // TCP port to connect to

$mail->setFrom('nav@teneo.ba', 'Employee Portal Info');

    //$mail->addAddress($email_admin2);
    //$mail->addAddress($email_user);
   //$mail->addAddress($email_stream_parent);

$mail->addAddress('nav@teneo.ba'); 
 // Add a recipient
//$mail->addBCC('bcc@example.com');

$mail->isHTML(true);  // Set email format to HTML
$bodyContent = '<h5 style="color:gray;font-weight:normal;">Poštovana/i, </h5 >' ;

$bodyContent .= '<h5 style="color:gray;font-weight:normal;">Ovim putem želimo Vas obavijestiti da je za zaposlenika'.' '.$user['fname'].' '.$user['lname'].' odbijen Zahtjev za Službeni put u periodu od '.date('d/m/Y',strtotime($row['h_from'])).' do '.date('d/m/Y',strtotime($row['h_to'])).'. </h5 >' ;
$bodyContent .= '<h5 style="color:gray;font-weight:normal;">Za više informacija, molimo Vas kontaktirajte kolegice Almiru Sadiković i/ili Lamiju Alajbegović. </h5 >' ;
$bodyContent .= '<h5 style="color:gray;font-weight:normal;">S poštovanjem, <br> Vaš HR Tim </h5 >' ;
$mail->Subject = 'Employee Portal!';
$mail->Body    = $bodyContent;


    
if(!$mail->send()) {
   echo 'Message could not be sent.';
   echo 'Mailer Error: ' . $mail->ErrorInfo;
} else { ?>
      

<?php }

    

  }

 else if($status_get==1){
require '../../send-email-from-localhost-php/PHPMailer/PHPMailerAutoload.php';

$mail = new PHPMailer(true);
$mail->CharSet = "UTF-8";


$mail->isSMTP(); 
//$mail->Host = '91.235.170.162';   
$mail->Host = gethostbyname('mail.teneo.ba');                   // Specify main and backup SMTP servers
$mail->SMTPAuth = true;                            // Enable SMTP authentication
$mail->Username = 'nav@teneo.ba';          // SMTP username
$mail->Password = 'DynamicsNAV16!'; // SMTP password

$mail->Port = 587;  


$mail->SMTPSecure = false;
$mail->SMTPAutoTLS = false;                              // TCP port to connect to

$mail->setFrom('nav@teneo.ba', 'Employee Portal Info');
    //$mail->addAddress($email_admin2);
    //$mail->addAddress($email_user);
   //$mail->addAddress($email_stream_parent);
$mail->addAddress('nav@teneo.ba'); 
 // Add a recipient

//$mail->addBCC('bcc@example.com');

$mail->isHTML(true);  // Set email format to HTML
$bodyContent = '<h5 style="color:gray;font-weight:normal;">Poštovana/i, </h5 >' ;

$bodyContent .= '<h5 style="color:gray;font-weight:normal;">Ovim putem želimo Vas obavijestiti da je za zaposlenika'.' '.$user['fname'].' '.$user['lname'].' odobren Zahtjev za Službeni put u periodu od '.date('d/m/Y',strtotime($row['h_from'])).' do '.date('d/m/Y',strtotime($row['h_to'])).'. </h5 >' ;
$bodyContent .= '<h5 style="color:gray;font-weight:normal;">Za više informacija, molimo Vas kontaktirajte kolegice Almiru Sadiković i/ili Lamiju Alajbegović. </h5 >' ;
$bodyContent .= '<h5 style="color:gray;font-weight:normal;">S poštovanjem, <br> Vaš HR Tim </h5 >' ;
$mail->Subject = 'Employee Portal!';
$mail->Body    = $bodyContent;


    
if(!$mail->send()) {
   echo 'Message could not be sent.';
   echo 'Mailer Error: ' . $mail->ErrorInfo;
} else { ?>
      

<?php }

    

  }
}




}




/******************************** ADMIN2 RESPONSE *****************************************************/

if($_POST['request']=='business-trip-request-response-admin2'){
     $_user = _user(_decrypt($_SESSION['SESSION_USER']));

     $status_get = $_POST['status_admin2'];


$get = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[business_trip] WHERE request_id='".$_POST['request_id']."'");
       if($get->rowCount()<0){
         $row = $get->fetch();
         $user = _user($row['user_id']);


      $query = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[business_trip] WHERE request_id='".$_POST['request_id']."'");
      $get2 = $db->query("SELECT COUNT(*) FROM [c0_intranet2_apoteke].[dbo].[business_trip]");
      $total = $get2->rowCount();
      foreach($query as $item){
          $tools_id = $item['request_id'];
         $request_parent=$item['parent'];
         $request_parent2=$item['parent2'];
         $request_stream = $item['hr'];
         $request_admin=$item['admin'];
         $request_admin2=$item['admin2'];
         $request_user=$item['user_id'];
         $get_country=$item['country_ino'];
      }
    }


   
    $stream_query  = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[users] WHERE employee_no='".$request_stream."'");

    foreach($stream_query as $uquery) {
    
    $email_stream_parent = $uquery['email'];
  
    }

     $parent_query  = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[users] WHERE employee_no='".$request_parent."'");

    foreach($parent_query as $uquery) {
    
    $email_parent = $uquery['email'];
  
    }


    $admin_query  = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[users] WHERE employee_no='".$request_admin."'");

    foreach($admin_query as $uquery) {
    
    $email_admin = $uquery['email'];
  
    }
  
    $admin2_query  = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[users] WHERE employee_no='".$request_admin2."'");

    foreach($admin2_query as $uquery) {
    
    $email_admin2 = $uquery['email'];
  
    }

    $user_query  = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[users] WHERE employee_no='".$request_user."'");

    foreach($user_query as $uquery) {
    
    $email_user = $uquery['email'];
  
    }
       
    date_default_timezone_set('Europe/Sarajevo');

    $data = "UPDATE [c0_intranet2_apoteke].[dbo].[business_trip] SET
      status_admin2_response = ?,
      date_response_status_admin2 = ?
      WHERE request_id = ?";

    $res = $db->prepare($data);
    $res->execute(
      array(
        $_POST['status_admin2'],
        date('Y-m-d H:i:s'),
        $_POST['request_id']
      )
    );
    if($res->rowCount()==1) {
    

      echo '<div class="alert alert-success text-center">'.__('Informacije su uspješno spašene!').'</div>';
    }


 if($get_country!=1) {
if($status_get==2){
require '../../send-email-from-localhost-php/PHPMailer/PHPMailerAutoload.php';

$mail = new PHPMailer(true);
$mail->CharSet = "UTF-8";


$mail->isSMTP(); 
//$mail->Host = '91.235.170.162';   
$mail->Host = gethostbyname('mail.teneo.ba');                   // Specify main and backup SMTP servers
$mail->SMTPAuth = true;                            // Enable SMTP authentication
$mail->Username = 'nav@teneo.ba';          // SMTP username
$mail->Password = 'DynamicsNAV16!'; // SMTP password

$mail->Port = 587;  


$mail->SMTPSecure = false;
$mail->SMTPAutoTLS = false;                              // TCP port to connect to

$mail->setFrom('nav@teneo.ba', 'Employee Portal Info');

    //$mail->addAddress($email_admin);
    //$mail->addAddress($email_user);
    //$mail-<addAddress($email_parent);
    //$mail-<addAddress($email_stream_parent);

$mail->addAddress('nav@teneo.ba'); 
 // Add a recipient
//$mail->addBCC('bcc@example.com');

$mail->isHTML(true);  // Set email format to HTML
$bodyContent = '<h5 style="color:gray;font-weight:normal;">Poštovana/i, </h5 >' ;

$bodyContent .= '<h5 style="color:gray;font-weight:normal;">Ovim putem želimo Vas obavijestiti da je za zaposlenika'.' '.$user['fname'].' '.$user['lname'].' odbijen Zahtjev za Službeni put u periodu od '.date('d/m/Y',strtotime($row['h_from'])).' do '.date('d/m/Y',strtotime($row['h_to'])).'. </h5 >' ;
$bodyContent .= '<h5 style="color:gray;font-weight:normal;">Za više informacija, molimo Vas kontaktirajte kolegice Almiru Sadiković i/ili Lamiju Alajbegović. </h5 >' ;
$bodyContent .= '<h5 style="color:gray;font-weight:normal;">S poštovanjem, <br> Vaš HR Tim </h5 >' ;
$mail->Subject = 'Employee Portal!';
$mail->Body    = $bodyContent;


    
if(!$mail->send()) {
   echo 'Message could not be sent.';
   echo 'Mailer Error: ' . $mail->ErrorInfo;
} else { ?>
      

<?php }

    

  }

 else if($status_get==1){
require '../../send-email-from-localhost-php/PHPMailer/PHPMailerAutoload.php';

$mail = new PHPMailer(true);
$mail->CharSet = "UTF-8";


$mail->isSMTP(); 
//$mail->Host = '91.235.170.162';   
$mail->Host = gethostbyname('mail.teneo.ba');                   // Specify main and backup SMTP servers
$mail->SMTPAuth = true;                            // Enable SMTP authentication
$mail->Username = 'nav@teneo.ba';          // SMTP username
$mail->Password = 'DynamicsNAV16!'; // SMTP password

$mail->Port = 587;  


$mail->SMTPSecure = false;
$mail->SMTPAutoTLS = false;                              // TCP port to connect to

$mail->setFrom('nav@teneo.ba', 'Employee Portal Info');
    //$mail->addAddress($email_admin);
    //$mail->addAddress($email_user);
    //$mail-<addAddress($email_parent);
    //$mail-<addAddress($email_stream_parent);
$mail->addAddress('nav@teneo.ba'); 
 // Add a recipient

//$mail->addBCC('bcc@example.com');

$mail->isHTML(true);  // Set email format to HTML
$bodyContent = '<h5 style="color:gray;font-weight:normal;">Poštovana/i, </h5 >' ;

$bodyContent .= '<h5 style="color:gray;font-weight:normal;">Ovim putem želimo Vas obavijestiti da je za zaposlenika'.' '.$user['fname'].' '.$user['lname'].' odobren Zahtjev za Službeni put u periodu od '.date('d/m/Y',strtotime($row['h_from'])).' do '.date('d/m/Y',strtotime($row['h_to'])).'. </h5 >' ;
$bodyContent .= '<h5 style="color:gray;font-weight:normal;">Za više informacija, molimo Vas kontaktirajte kolegice Almiru Sadiković i/ili Lamiju Alajbegović. </h5 >' ;
$bodyContent .= '<h5 style="color:gray;font-weight:normal;">S poštovanjem, <br> Vaš HR Tim </h5 >' ;
$mail->Subject = 'Employee Portal!';
$mail->Body    = $bodyContent;


    
if(!$mail->send()) {
   echo 'Message could not be sent.';
   echo 'Mailer Error: ' . $mail->ErrorInfo;
} else { ?>
      

<?php }

    

  }
}


if($get_country==1) {
if($status_get==2){
require '../../send-email-from-localhost-php/PHPMailer/PHPMailerAutoload.php';

$mail = new PHPMailer(true);
$mail->CharSet = "UTF-8";


$mail->isSMTP(); 
//$mail->Host = '91.235.170.162';   
$mail->Host = gethostbyname('mail.teneo.ba');                   // Specify main and backup SMTP servers
$mail->SMTPAuth = true;                            // Enable SMTP authentication
$mail->Username = 'nav@teneo.ba';          // SMTP username
$mail->Password = 'DynamicsNAV16!'; // SMTP password

$mail->Port = 587;  


$mail->SMTPSecure = false;
$mail->SMTPAutoTLS = false;                              // TCP port to connect to

$mail->setFrom('nav@teneo.ba', 'Employee Portal Info');

    //$mail->addAddress($email_admin);
    //$mail->addAddress($email_user);
   //$mail->addAddress($email_stream_parent);

$mail->addAddress('nav@teneo.ba'); 
 // Add a recipient
//$mail->addBCC('bcc@example.com');

$mail->isHTML(true);  // Set email format to HTML
$bodyContent = '<h5 style="color:gray;font-weight:normal;">Poštovana/i, </h5 >' ;

$bodyContent .= '<h5 style="color:gray;font-weight:normal;">Ovim putem želimo Vas obavijestiti da je za zaposlenika'.' '.$user['fname'].' '.$user['lname'].' odbijen Zahtjev za Službeni put u periodu od '.date('d/m/Y',strtotime($row['h_from'])).' do '.date('d/m/Y',strtotime($row['h_to'])).'. </h5 >' ;
$bodyContent .= '<h5 style="color:gray;font-weight:normal;">Za više informacija, molimo Vas kontaktirajte kolegice Almiru Sadiković i/ili Lamiju Alajbegović. </h5 >' ;
$bodyContent .= '<h5 style="color:gray;font-weight:normal;">S poštovanjem, <br> Vaš HR Tim </h5 >' ;
$mail->Subject = 'Employee Portal!';
$mail->Body    = $bodyContent;


    
if(!$mail->send()) {
   echo 'Message could not be sent.';
   echo 'Mailer Error: ' . $mail->ErrorInfo;
} else { ?>
      

<?php }

    

  }

 else if($status_get==1){
require '../../send-email-from-localhost-php/PHPMailer/PHPMailerAutoload.php';

$mail = new PHPMailer(true);
$mail->CharSet = "UTF-8";


$mail->isSMTP(); 
//$mail->Host = '91.235.170.162';   
$mail->Host = gethostbyname('mail.teneo.ba');                   // Specify main and backup SMTP servers
$mail->SMTPAuth = true;                            // Enable SMTP authentication
$mail->Username = 'nav@teneo.ba';          // SMTP username
$mail->Password = 'DynamicsNAV16!'; // SMTP password

$mail->Port = 587;  


$mail->SMTPSecure = false;
$mail->SMTPAutoTLS = false;                              // TCP port to connect to

$mail->setFrom('nav@teneo.ba', 'Employee Portal Info');
    //$mail->addAddress($email_admin);
    //$mail->addAddress($email_user);
   //$mail->addAddress($email_stream_parent);
$mail->addAddress('nav@teneo.ba'); 
 // Add a recipient

//$mail->addBCC('bcc@example.com');

$mail->isHTML(true);  // Set email format to HTML
$bodyContent = '<h5 style="color:gray;font-weight:normal;">Poštovana/i, </h5 >' ;

$bodyContent .= '<h5 style="color:gray;font-weight:normal;">Ovim putem želimo Vas obavijestiti da je za zaposlenika'.' '.$user['fname'].' '.$user['lname'].' odobren Zahtjev za Službeni put u periodu od '.date('d/m/Y',strtotime($row['h_from'])).' do '.date('d/m/Y',strtotime($row['h_to'])).'. </h5 >' ;
$bodyContent .= '<h5 style="color:gray;font-weight:normal;">Za više informacija, molimo Vas kontaktirajte kolegice Almiru Sadiković i/ili Lamiju Alajbegović. </h5 >' ;
$bodyContent .= '<h5 style="color:gray;font-weight:normal;">S poštovanjem, <br> Vaš HR Tim </h5 >' ;
$mail->Subject = 'Employee Portal!';
$mail->Body    = $bodyContent;


    
if(!$mail->send()) {
   echo 'Message could not be sent.';
   echo 'Mailer Error: ' . $mail->ErrorInfo;
} else { ?>
      

<?php }

    

  }
}



}



 if($_POST['request']=='business-trip-request-response2'){
     $_user = _user(_decrypt($_SESSION['SESSION_USER']));

     $status_get = $_POST['status_hr'];


$get = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[business_trip] WHERE request_id='".$_POST['request_id']."'");
       if($get->rowCount()<0){
         $row = $get->fetch();
         $user = _user($row['user_id']);


      $query = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[business_trip] WHERE request_id='".$_POST['request_id']."'");
      $get2 = $db->query("SELECT COUNT(*) FROM [c0_intranet2_apoteke].[dbo].[business_trip]");
      $total = $get2->rowCount();
      foreach($query as $item){
         $tools_id = $item['request_id'];
         $request_parent=$item['parent'];
         $request_parent2=$item['parent2'];
         $request_stream = $item['hr'];
         $request_admin=$item['admin'];
         $request_admin2=$item['admin2'];
         $request_user=$item['user_id'];
         $get_country=$item['country_ino'];
      }
    }


   
    $stream_query  = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[users] WHERE employee_no='".$request_stream."'");

    foreach($stream_query as $uquery) {
    
    $email_stream_parent = $uquery['email'];
  
    }

     $parent_query  = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[users] WHERE employee_no='".$request_parent."'");

    foreach($parent_query as $uquery) {
    
    $email_parent = $uquery['email'];
  
    }


    $admin_query  = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[users] WHERE employee_no='".$request_admin."'");

    foreach($admin_query as $uquery) {
    
    $email_admin = $uquery['email'];
  
    }
  
    $admin2_query  = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[users] WHERE employee_no='".$request_admin2."'");

    foreach($admin2_query as $uquery) {
    
    $email_admin2 = $uquery['email'];
  
    }

    $user_query  = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[users] WHERE employee_no='".$request_user."'");

    foreach($user_query as $uquery) {
    
    $email_user = $uquery['email'];
  
    }

    date_default_timezone_set('Europe/Sarajevo');

    $data = "UPDATE [c0_intranet2_apoteke].[dbo].[business_trip] SET
      status_hr = ?,
      date_response_hr = ?
      WHERE request_id = ?";

    $res = $db->prepare($data);
    $res->execute(
      array(
        $_POST['status_hr'],
        date('Y-m-d H:i:s'),
        $_POST['request_id']
      )
    );
    if($res->rowCount()==1) {
      echo '<div class="alert alert-success text-center">'.__('Informacije su uspješno spašene!').'</div>';
    }

  
if($get_country!=1) {
if($status_get==2){
require '../../send-email-from-localhost-php/PHPMailer/PHPMailerAutoload.php';

$mail = new PHPMailer(true);
$mail->CharSet = "UTF-8";


$mail->isSMTP(); 
//$mail->Host = '91.235.170.162';   
$mail->Host = gethostbyname('mail.teneo.ba');                   // Specify main and backup SMTP servers
$mail->SMTPAuth = true;                            // Enable SMTP authentication
$mail->Username = 'nav@teneo.ba';          // SMTP username
$mail->Password = 'DynamicsNAV16!'; // SMTP password

$mail->Port = 587;  


$mail->SMTPSecure = false;
$mail->SMTPAutoTLS = false;                              // TCP port to connect to

$mail->setFrom('nav@teneo.ba', 'Employee Portal Info');

    //$mail->addAddress($email_admin);
    //$mail->addAddress($email_admin2);
    //$mail->addAddress($email_user);
    //$mail-<addAddress($email_parent);

$mail->addAddress('nav@teneo.ba'); 
 // Add a recipient
//$mail->addBCC('bcc@example.com');

$mail->isHTML(true);  // Set email format to HTML
$bodyContent = '<h5 style="color:gray;font-weight:normal;">Poštovana/i, </h5 >' ;

$bodyContent .= '<h5 style="color:gray;font-weight:normal;">Ovim putem želimo Vas obavijestiti da je za zaposlenika'.' '.$user['fname'].' '.$user['lname'].' odbijen Zahtjev za Službeni put u periodu od '.date('d/m/Y',strtotime($row['h_from'])).' do '.date('d/m/Y',strtotime($row['h_to'])).'. </h5 >' ;
$bodyContent .= '<h5 style="color:gray;font-weight:normal;">Za više informacija, molimo Vas kontaktirajte kolegice Almiru Sadiković i/ili Lamiju Alajbegović. </h5 >' ;
$bodyContent .= '<h5 style="color:gray;font-weight:normal;">S poštovanjem, <br> Vaš HR Tim </h5 >' ;
$mail->Subject = 'Employee Portal!';
$mail->Body    = $bodyContent;


    
if(!$mail->send()) {
   echo 'Message could not be sent.';
   echo 'Mailer Error: ' . $mail->ErrorInfo;
} else { ?>
      

<?php }

    

  }

 else if($status_get==1){
require '../../send-email-from-localhost-php/PHPMailer/PHPMailerAutoload.php';

$mail = new PHPMailer(true);
$mail->CharSet = "UTF-8";


$mail->isSMTP(); 
//$mail->Host = '91.235.170.162';   
$mail->Host = gethostbyname('mail.teneo.ba');                   // Specify main and backup SMTP servers
$mail->SMTPAuth = true;                            // Enable SMTP authentication
$mail->Username = 'nav@teneo.ba';          // SMTP username
$mail->Password = 'DynamicsNAV16!'; // SMTP password

$mail->Port = 587;  


$mail->SMTPSecure = false;
$mail->SMTPAutoTLS = false;                              // TCP port to connect to

$mail->setFrom('nav@teneo.ba', 'Employee Portal Info');
    //$mail->addAddress($email_admin);
    //$mail->addAddress($email_admin2);
    //$mail->addAddress($email_user);
    //$mail-<addAddress($email_parent);
$mail->addAddress('nav@teneo.ba'); 
 // Add a recipient

//$mail->addBCC('bcc@example.com');

$mail->isHTML(true);  // Set email format to HTML
$bodyContent = '<h5 style="color:gray;font-weight:normal;">Poštovana/i, </h5 >' ;

$bodyContent .= '<h5 style="color:gray;font-weight:normal;">Ovim putem želimo Vas obavijestiti da je za zaposlenika'.' '.$user['fname'].' '.$user['lname'].' odobren Zahtjev za Službeni put u periodu od '.date('d/m/Y',strtotime($row['h_from'])).' do '.date('d/m/Y',strtotime($row['h_to'])).'. </h5 >' ;
$bodyContent .= '<h5 style="color:gray;font-weight:normal;">Za više informacija, molimo Vas kontaktirajte kolegice Almiru Sadiković i/ili Lamiju Alajbegović. </h5 >' ;
$bodyContent .= '<h5 style="color:gray;font-weight:normal;">S poštovanjem, <br> Vaš HR Tim </h5 >' ;
$mail->Subject = 'Employee Portal!';
$mail->Body    = $bodyContent;


    
if(!$mail->send()) {
   echo 'Message could not be sent.';
   echo 'Mailer Error: ' . $mail->ErrorInfo;
} else { ?>
      

<?php }

    

  }
}


if($get_country==1) {
if($status_get==2){
require '../../send-email-from-localhost-php/PHPMailer/PHPMailerAutoload.php';

$mail = new PHPMailer(true);
$mail->CharSet = "UTF-8";


$mail->isSMTP(); 
//$mail->Host = '91.235.170.162';   
$mail->Host = gethostbyname('mail.teneo.ba');                   // Specify main and backup SMTP servers
$mail->SMTPAuth = true;                            // Enable SMTP authentication
$mail->Username = 'nav@teneo.ba';          // SMTP username
$mail->Password = 'DynamicsNAV16!'; // SMTP password

$mail->Port = 587;  


$mail->SMTPSecure = false;
$mail->SMTPAutoTLS = false;                              // TCP port to connect to

$mail->setFrom('nav@teneo.ba', 'Employee Portal Info');

    //$mail->addAddress($email_admin);
    //$mail->addAddress($email_admin2);
    //$mail->addAddress($email_user);

$mail->addAddress('nav@teneo.ba'); 
 // Add a recipient
//$mail->addBCC('bcc@example.com');

$mail->isHTML(true);  // Set email format to HTML
$bodyContent = '<h5 style="color:gray;font-weight:normal;">Poštovana/i, </h5 >' ;

$bodyContent .= '<h5 style="color:gray;font-weight:normal;">Ovim putem želimo Vas obavijestiti da je za zaposlenika'.' '.$user['fname'].' '.$user['lname'].' odbijen Zahtjev za Službeni put u periodu od '.date('d/m/Y',strtotime($row['h_from'])).' do '.date('d/m/Y',strtotime($row['h_to'])).'. </h5 >' ;
$bodyContent .= '<h5 style="color:gray;font-weight:normal;">Za više informacija, molimo Vas kontaktirajte kolegice Almiru Sadiković i/ili Lamiju Alajbegović. </h5 >' ;
$bodyContent .= '<h5 style="color:gray;font-weight:normal;">S poštovanjem, <br> Vaš HR Tim </h5 >' ;
$mail->Subject = 'Employee Portal!';
$mail->Body    = $bodyContent;


    
if(!$mail->send()) {
   echo 'Message could not be sent.';
   echo 'Mailer Error: ' . $mail->ErrorInfo;
} else { ?>
      

<?php }

    

  }

 else if($status_get==1){
require '../../send-email-from-localhost-php/PHPMailer/PHPMailerAutoload.php';

$mail = new PHPMailer(true);
$mail->CharSet = "UTF-8";


$mail->isSMTP(); 
//$mail->Host = '91.235.170.162';   
$mail->Host = gethostbyname('mail.teneo.ba');                   // Specify main and backup SMTP servers
$mail->SMTPAuth = true;                            // Enable SMTP authentication
$mail->Username = 'nav@teneo.ba';          // SMTP username
$mail->Password = 'DynamicsNAV16!'; // SMTP password

$mail->Port = 587;  


$mail->SMTPSecure = false;
$mail->SMTPAutoTLS = false;                              // TCP port to connect to

$mail->setFrom('nav@teneo.ba', 'Employee Portal Info');
    //$mail->addAddress($email_admin);
    //$mail->addAddress($email_admin2);
    //$mail->addAddress($email_user);
$mail->addAddress('nav@teneo.ba'); 
 // Add a recipient

//$mail->addBCC('bcc@example.com');

$mail->isHTML(true);  // Set email format to HTML
$bodyContent = '<h5 style="color:gray;font-weight:normal;">Poštovana/i, </h5 >' ;

$bodyContent .= '<h5 style="color:gray;font-weight:normal;">Ovim putem želimo Vas obavijestiti da je za zaposlenika'.' '.$user['fname'].' '.$user['lname'].' odobren Zahtjev za Službeni put u periodu od '.date('d/m/Y',strtotime($row['h_from'])).' do '.date('d/m/Y',strtotime($row['h_to'])).'. </h5 >' ;
$bodyContent .= '<h5 style="color:gray;font-weight:normal;">Za više informacija, molimo Vas kontaktirajte kolegice Almiru Sadiković i/ili Lamiju Alajbegović. </h5 >' ;
$bodyContent .= '<h5 style="color:gray;font-weight:normal;">S poštovanjem, <br> Vaš HR Tim </h5 >' ;
$mail->Subject = 'Employee Portal!';
$mail->Body    = $bodyContent;


    
if(!$mail->send()) {
   echo 'Message could not be sent.';
   echo 'Mailer Error: ' . $mail->ErrorInfo;
} else { ?>
      

<?php }

    

  }
}


}



 if($_POST['request']=='business-trip-request-response2-parent2'){

     $_user = _user(_decrypt($_SESSION['SESSION_USER']));

      $status_get = $_POST['status_parent2'];


$get = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[business_trip] WHERE request_id='".$_POST['request_id']."'");
       if($get->rowCount()<0){
         $row = $get->fetch();
         $user = _user($row['user_id']);


      $query = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[business_trip] WHERE request_id='".$_POST['request_id']."'");
      $get2 = $db->query("SELECT COUNT(*) FROM [c0_intranet2_apoteke].[dbo].[business_trip]");
      $total = $get2->rowCount();
      foreach($query as $item){
         $tools_id = $item['request_id'];
         $request_parent=$item['parent'];
         $request_parent2=$item['parent2'];
         $request_stream = $item['hr'];
         $request_user=$item['user_id'];
         $get_country=$item['country_ino'];
      }
    }


    $parent_query  = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[users] WHERE employee_no='".$request_parent."'");

    foreach($parent_query as $uquery) {
    
    $email = $uquery['email'];
  
    }


    $stream_query  = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[users] WHERE employee_no='".$request_stream."'");

    foreach($stream_query as $uquery) {
    
    $email_stream = $uquery['email'];
  
    }

     $parent2_query  = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[users] WHERE employee_no='".$request_parent2."'");

    foreach($parent2_query as $uquery) {
    
    $email_parent2 = $uquery['email'];
  
    }
  
    $user_query  = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[users] WHERE user_id='".$request_user."'");

    foreach($user_query as $uquery) {
    
    $email_user = $uquery['email'];
  
    }


 
    date_default_timezone_set('Europe/Sarajevo');
     //echo date('Y-m-d H:i:s T', time()) . "<br>\n";

   $data = "UPDATE [c0_intranet2_apoteke].[dbo].[business_trip] SET
      status_parent2 = ?,
      comment_parent2 = ?,
      date_response_parent2 = ?
      WHERE request_id = ?";

    $res = $db->prepare($data);
    $res->execute(
      array(
        $_POST['status_parent2'],
        $_POST['comment_parent2'],
        date('Y-m-d H:i:s'),
        $_POST['request_id']
      )
    );
    if($res->rowCount()==1) {
      echo '<div class="alert alert-success text-center">'.__('Informacije su uspješno spašene!').'</div>';
    }

if($get_country==1) {
if($status_get==2){
require '../../send-email-from-localhost-php/PHPMailer/PHPMailerAutoload.php';

$mail = new PHPMailer(true);
$mail->CharSet = "UTF-8";


$mail->isSMTP(); 
//$mail->Host = '91.235.170.162';   
$mail->Host = gethostbyname('mail.teneo.ba');                   // Specify main and backup SMTP servers
$mail->SMTPAuth = true;                            // Enable SMTP authentication
$mail->Username = 'nav@teneo.ba';          // SMTP username
$mail->Password = 'DynamicsNAV16!'; // SMTP password

$mail->Port = 587;  


$mail->SMTPSecure = false;
$mail->SMTPAutoTLS = false;                              // TCP port to connect to

$mail->setFrom('nav@teneo.ba', 'Employee Portal Info');
//$mail->addReplyTo('irma.hrelja@infodom.ba', 'Irma');
//$mail->addAddress($email);  
 //$mail->addAddress('irma.hrelja@infodom.ba'); 
    //$mail->addAddress($email);
    //$mail->addAddress($email_parent2);
    //$mail->addAddress($email_user);
    //echo $email;
    //echo $email_parent2;
    //echo $email_user;


 // Add a recipient
//$mail->addCC('nermina.kraljic@infodom.ba');
//$mail->addBCC('bcc@example.com');
    $mail->addAddress('nav@teneo.ba'); 

$mail->isHTML(true);  // Set email format to HTML
$bodyContent = '<h5 style="color:gray;font-weight:normal;">Poštovana/i, </h5 >' ;

$bodyContent .= '<h5 style="color:gray;font-weight:normal;">Ovim putem želimo Vas obavijestiti da je za zaposlenika'.' '.$user['fname'].' '.$user['lname'].' odbijen Zahtjev za Službeni put u periodu od '.date('d/m/Y',strtotime($row['h_from'])).' do '.date('d/m/Y',strtotime($row['h_to'])).'. </h5 >' ;
$bodyContent .= '<a href=http://infodom-server.sarajevo-infodom.ba:81/app/?m=business_trip2&p=popup_business_trip_add_view_parent&id='.$tools_id.'>Za direktan pristup pregleda Zahtjeva, kliknite ovdje.</a>';
$bodyContent .= '<h5 style="color:gray;font-weight:normal;">S poštovanjem, <br> Vaš HR Tim </h5 >' ;
$mail->Subject = 'Employee Portal!';
$mail->Body    = $bodyContent;

    
if(!$mail->send()) {
   echo 'Message could not be sent.';
   echo 'Mailer Error: ' . $mail->ErrorInfo;
} else { ?>
      

<?php }

    

  }


 else if($status_get==1){
require '../../send-email-from-localhost-php/PHPMailer/PHPMailerAutoload.php';

$mail = new PHPMailer(true);
$mail->CharSet = "UTF-8";


$mail->isSMTP(); 
//$mail->Host = '91.235.170.162';   
$mail->Host = gethostbyname('mail.teneo.ba');                   // Specify main and backup SMTP servers
$mail->SMTPAuth = true;                            // Enable SMTP authentication
$mail->Username = 'nav@teneo.ba';          // SMTP username
$mail->Password = 'DynamicsNAV16!'; // SMTP password

$mail->Port = 587;  


$mail->SMTPSecure = false;
$mail->SMTPAutoTLS = false;                              // TCP port to connect to

$mail->setFrom('nav@teneo.ba', 'Employee Portal Info');
//$mail->addReplyTo('irma.hrelja@infodom.ba', 'Irma');
//$mail->addAddress($email);  
 //$mail->addAddress('irma.hrelja@infodom.ba');
    //$mail->addAddress( $email);
    //$mail->addAddress($email_parent2);
    //$mail->addAddress($email_user);
    $mail->addAddress('nav@teneo.ba'); 
    //echo $email;
    //echo $email_parent2;
    //echo $email_user;
 // Add a recipient
//$mail->addCC('nermina.kraljic@infodom.ba');
//$mail->addBCC('bcc@example.com');

$mail->isHTML(true);  // Set email format to HTML
$bodyContent = '<h5 style="color:gray;font-weight:normal;">Poštovana/i, </h5 >' ;

$bodyContent .= '<h5 style="color:gray;font-weight:normal;">Ovim putem želimo Vas obavijestiti da je za zaposlenika'.' '.$user['fname'].' '.$user['lname'].' odobren Službeni put u periodu od '.date('d/m/Y',strtotime($row['h_from'])).' do '.date('d/m/Y',strtotime($row['h_to'])).'. </h5 >' ;
$bodyContent .= '<a href=http://infodom-server.sarajevo-infodom.ba:81/app/?m=business_trip&p=popup_business_trip2_reponse_parent2&id='.$tools_id.'>Za direktan pristup pregleda Zahtjeva, kliknite ovdje.</a>';
$bodyContent .= '<h5 style="color:gray;font-weight:normal;">S poštovanjem, <br> Vaš HR Tim </h5 >' ;
$mail->Subject = 'Employee Portal!';
$mail->Body    = $bodyContent;

    
if(!$mail->send()) {
   echo 'Message could not be sent.';
   echo 'Mailer Error: ' . $mail->ErrorInfo;
} else { ?>
      

<?php }

    

  }
}


/*******************INO SATNICE MAIL***********************************/

if($get_country!=1) {
if($status_get==2){
require '../../send-email-from-localhost-php/PHPMailer/PHPMailerAutoload.php';

$mail = new PHPMailer(true);
$mail->CharSet = "UTF-8";


$mail->isSMTP(); 
//$mail->Host = '91.235.170.162';   
$mail->Host = gethostbyname('mail.teneo.ba');                   // Specify main and backup SMTP servers
$mail->SMTPAuth = true;                            // Enable SMTP authentication
$mail->Username = 'nav@teneo.ba';          // SMTP username
$mail->Password = 'DynamicsNAV16!'; // SMTP password

$mail->Port = 587;  


$mail->SMTPSecure = false;
$mail->SMTPAutoTLS = false;                              // TCP port to connect to

$mail->setFrom('nav@teneo.ba', 'Employee Portal Info');


    //$mail->addAddress($email);
    //$mail->addAddress($email_parent2);
    //$mail->addAddress($email_user);
    //$mail-<addAddress($email_stream);

    $mail->addAddress('nav@teneo.ba'); 
 // Add a recipient
//$mail->addCC('nermina.kraljic@infodom.ba');
//$mail->addBCC('bcc@example.com');

$mail->isHTML(true);  // Set email format to HTML
$bodyContent = '<h5 style="color:gray;font-weight:normal;">Poštovana/i, </h5 >' ;

$bodyContent .= '<h5 style="color:gray;font-weight:normal;">Ovim putem želimo Vas obavijestiti da je za zaposlenika'.' '.$user['fname'].' '.$user['lname'].' odbijen Zahtjev za Službeni put u periodu od '.date('d/m/Y',strtotime($row['h_from'])).' do '.date('d/m/Y',strtotime($row['h_to'])).'. </h5 >' ;
$bodyContent .= '<a href=http://infodom-server.sarajevo-infodom.ba:81/app/?m=business_trip2&p=popup_business_trip_add_view_parent&id='.$tools_id.'>Za direktan pristup pregleda Zahtjeva, kliknite ovdje.</a>';
$bodyContent .= '<h5 style="color:gray;font-weight:normal;">S poštovanjem, <br> Vaš HR Tim </h5 >' ;
$mail->Subject = 'Employee Portal!';
$mail->Body    = $bodyContent;

    
if(!$mail->send()) {
   echo 'Message could not be sent.';
   echo 'Mailer Error: ' . $mail->ErrorInfo;
} else { ?>
      

<?php }

    

  }


 else if($status_get==1){
require '../../send-email-from-localhost-php/PHPMailer/PHPMailerAutoload.php';

$mail = new PHPMailer(true);
$mail->CharSet = "UTF-8";


$mail->isSMTP(); 
//$mail->Host = '91.235.170.162';   
$mail->Host = gethostbyname('mail.teneo.ba');                   // Specify main and backup SMTP servers
$mail->SMTPAuth = true;                            // Enable SMTP authentication
$mail->Username = 'nav@teneo.ba';          // SMTP username
$mail->Password = 'DynamicsNAV16!'; // SMTP password

$mail->Port = 587;  


$mail->SMTPSecure = false;
$mail->SMTPAutoTLS = false;                              // TCP port to connect to

$mail->setFrom('nav@teneo.ba', 'Employee Portal Info');
$mail->addAddress('nav@teneo.ba'); 
 

    //$mail-<addAddress($email_stream);

 // Add a recipient
//$mail->addCC('nermina.kraljic@infodom.ba');
//$mail->addBCC('bcc@example.com');

$mail->isHTML(true);  // Set email format to HTML
$bodyContent = '<h5 style="color:gray;font-weight:normal;">Poštovana/i, </h5 >' ;

$bodyContent .= '<h5 style="color:gray;font-weight:normal;">Ovim putem želimo Vas obavijestiti da je za zaposlenika'.' '.$user['fname'].' '.$user['lname'].' odobren Službeni put u periodu od '.date('d/m/Y',strtotime($row['h_from'])).' do '.date('d/m/Y',strtotime($row['h_to'])).'. </h5 >' ;
$bodyContent .= '<a href=http://infodom-server.sarajevo-infodom.ba:81/app/?m=business_trip&p=popup_business_trip2_reponse&id='.$tools_id.'>Za direktan pristup odobravanju Zahtjeva, kliknite ovdje.</a>';
$bodyContent .= '<h5 style="color:gray;font-weight:normal;">S poštovanjem, <br> Vaš HR Tim </h5 >' ;
$mail->Subject = 'Employee Portal!';
$mail->Body    = $bodyContent;

    
if(!$mail->send()) {
   echo 'Message could not be sent.';
   echo 'Mailer Error: ' . $mail->ErrorInfo;
} else { ?>
      

<?php }

    

  }
}



}

}


?>
