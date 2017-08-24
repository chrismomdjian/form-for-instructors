<?php
  // include the class
  require "mailer/PHPMailerAutoload.php";

  // create an instance of PHPMailer
  $mail = new PHPMailer();

  // enable smtp
  $mail->Host = "smtp.gmail.com";

  // enable SMTP
  //$mail->isSMTP();

  // set authentication to true
  $mail->SMTPAuth = true;

  // set login details for gmail account (to SEND email)
  $mail->Username = "chrismomdjian@gmail.com";
  $mail->Password = "filmerhands";

  // set type of protection
  $mail->SMTPSecure = "ssl"; // OR TLS

  // set a port
  $mail->Port = "465"; // 587 if TLS

  // set Subject
  $mail->Subject = "test email";

  // set Body
  $mail->Body = "This is our body";

  // set who is sending the email
  $mail->setFrom("chrismomdjian@gmail.com");

  // set where we are sending the email (recipients)
  $mail->addAddress("CMOMDJI579@student.glendale.edu");

  // send an email
  if($mail->send()) {
    echo "Message sent!";
  } else {
    echo $mail->ErrorInfo;
  }
?>
