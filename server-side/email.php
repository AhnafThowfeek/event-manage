<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendMailByPayments($client_name,$client_email, $paymentURL){

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();                                             // Set mailer to use SMTP
        $mail->Host = 'smtp.gmail.com';                             // Set the SMTP server to send through
        $mail->SMTPAuth = true;                                       // Enable SMTP authentication
        $mail->Username = 'mtmahnaf21@gmail.com';                   // SMTP username
        $mail->Password = 'xyzx jrye rfap melr';                            // SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;           // Enable TLS encryption
        $mail->Port = 465;                                            // TCP port to connect to
    
        //Recipients
        $mail->setFrom('mtmahnaf21@gmail.com', 'EventPro');
        $mail->addAddress($client_email, $client_name);       // Add a recipient
    
        // Content
        $mail->isHTML(true);                                          // Set email format to HTML
        $mail->Subject = 'Test Email using PHPMailer';
        $mail->Body    = "<b>Hello, this is a test email sent using EventPro! <a href='$paymentURL'>Pay Now</a></b>";
        $mail->AltBody = 'Hello, this is a test email sent using PHPMailer!';

        $mail->send();
        echo 'Email has been sent';
        return true;
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        return false;
    }
}
