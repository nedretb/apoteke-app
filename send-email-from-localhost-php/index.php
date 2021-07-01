<?php
require 'PHPMailer/PHPMailerAutoload.php';

$mail = new PHPMailer(true);


$mail->isSMTP(); 
//$mail->SMTPDebug = 4; 
                                 // Set mailer to use SMTP
$mail->Host = 'smtp.gmail.com';                    // Specify main and backup SMTP servers
$mail->SMTPAuth = true;                            // Enable SMTP authentication
$mail->Username = 'employee.portal.info@gmail.com';          // SMTP username
$mail->Password = 'Pocetni2016!'; // SMTP password
//$mail->SMTPSecure = 'tls';                         // Enable TLS encryption, `ssl` also accepted

$mail->Port = 587;                                 // TCP port to connect to

$mail->setFrom('irma.hrelja@gmail.com', 'Irma');
$mail->addReplyTo('irma.hrelja@gmail.com', 'Irma');
$mail->addAddress('nermina.agovic@gmail.com');   // Add a recipient
//$mail->addCC('cc@example.com');
//$mail->addBCC('bcc@example.com');

$mail->isHTML(true);  // Set email format to HTML

$bodyContent = '<h1>How to Send Email using PHP in Localhost</h1>';
$bodyContent .= '<p>This is the HTML email sent from localhost using PHP script by <b>CodexWorld</b></p>';

$mail->Subject = 'Mail test';
$mail->Body    = $bodyContent;

if(!$mail->send()) {
    echo 'Message could not be sent.';
    echo 'Mailer Error: ' . $mail->ErrorInfo;
} else {
    echo 'Message has been sent';
}
?>
