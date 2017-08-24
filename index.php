<?php
//*******************************************************************
// This form is meant to allow GCC instructors to enter their
// credentials and part-time or full-time hours, as well as
// submit a pdf form to a database and email them (via the email
// specified) all of the information entered in the form.
//
// - Christian Momdjian (8/17/2017)
//*******************************************************************

//require "inc/connect.php";     // To connect to database

$error_message = "";
$success_message = "";

// If submit button is clicked, run all checks
if(isset($_POST["send_data"])) {

  // If all fields are empty, display an error message
  if(!empty($_POST["full_name"]) && !empty($_POST["email"]) && !empty($_POST["hours"])) {

    // If email is not valid, display an email error message
    if(!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
      $error_message = "Please enter a valid email.";
    } else {
      $name   = clean_input($_POST["full_name"]);
      $email  = clean_input($_POST["email"]);
      $hours  = clean_input($_POST["hours"]);

      // Checks to see if file uploaded is of type 'pdf'
      if ($_FILES['file_to_upload']['error'] !== UPLOAD_ERR_OK):
          //die("Upload failed with error: " . $_FILES['file_to_upload']['error']);
          $error_message = "Please provide a pdf document.";
      else:
          $finfo  = finfo_open(FILEINFO_MIME_TYPE);
          $mime   = finfo_file($finfo, $_FILES['file_to_upload']['tmp_name']);

          if($mime == "application/pdf"):
            $upload_state = "Successful";
          else:
            $error_message  = "This filetype: " . $mime . " is not accepted.";
            $upload_state   = "Failed";
          endif;
      endif;

      // If file uploaded is indeed a pdf, then proceed with handling email and other tasks
      if($upload_state === "Successful"):
        $pdf_name = addslashes($_FILES["file_to_upload"]["name"]);
        $pdf_temp_location = addslashes($_FILES["file_to_upload"]["tmp_name"]);
        $pdf_contents = addslashes(file_get_contents($_FILES["file_to_upload"]["tmp_name"]));

        /////////////////////////////////////////
        ///////////// HANDLE EMAIL //////////////
        /////////////////////////////////////////

        // Constructing the BODY of the email to be sent
        $body = "This is the information you provided in the form:\n\n";
        $body .= "Name: " . $name . "\n";
        $body .= "Email Address: " . $email . "\n";
        $body .= "Hours: " . $hours . "\n";
        $body .= "--- end of email ---";

        require "mailer/PHPMailerAutoload.php";               // include the PHPMailer class
        $mail = new PHPMailer();                              // create an instance of PHPMailer
        $mail->Host = "smtp.gmail.com";                       // choose a host
        $mail->SMTPAuth = true;                               // set authentication to true
        $mail->Username = "chrismomdjian@gmail.com";          // set login details for gmail account (to SEND email)
        require "inc/pass.php";
        $mail->SMTPSecure = "ssl"; // or tls                  // set type of protection
        $mail->Port = "465"; // 587 if tls                    // set a port
        $mail->Subject = "Information from GCC";              // set subject
        $mail->Body = $body;                                  // set body
        $mail->setFrom("chrismomdjian@gmail.com");            // set who is sending the email
        $mail->addAddress($email);                            // set where we are sending the email to (recipients)

        // send the email
        if($mail->send()) {
          $success_message = "Your form has been submitted! We have sent you an email.";
        } else {
          $error_message = "Sorry. Looks like the email didn't go through.";
        }
      endif;

    }
  } else {
    $error_message = "Please fill in all fields.";
  }
}

// Sanitizes user input
function clean_input($data) {
  $data = htmlspecialchars($data);
  $data = stripslashes($data);
  $data = trim($data);
  return $data;
}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title>GCC Schedule System</title>
		<meta charset="utf-8">
    <!-- BOOTSTRAP 4 CDN (for mobile-responsiveness and browser compatibility) -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous">
    <!-- Personalized Stylesheet -->
    <link rel="stylesheet" href="style.css">
	</head>

	<body>
		<div class="container">
      <div class="text-center">
        <!-- GCC Logo -->
        <a href="https://glendale.edu" target="_blank">
          <img src="gcclogo.jpg" class="rounded-circle img-fluid" alt="Glendale Community College logo" title="Glendale Community College"/>
        </a>
      </div>

      <!-- Form Heading -->
			<h1 class="display-5 text-center form-title">Enter your information</h1>

			<div class="form-section">
        <hr>
        <!-- SUCCESS AND ERROR MESSAGES -->
        <?php if(!empty($error_message)): ?>
          <p class="error-message"><?php echo $error_message; ?></p>
        <?php endif; ?>

        <?php if(!empty($success_message)): ?>
          <p class="success-message"><?php echo $success_message; ?></p>
        <?php endif; ?>

        <!-- Form data is sent to itself using HTTP POST method -->
				<form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST" enctype="multipart/form-data">

          <!-- Field for Name -->
					<div class="form-group">
						<input id="name-field" placeholder="Full Name" type="text" name="full_name" class="form-control" value="<?php if(isset($_POST['send_data'])){echo $_POST["full_name"];}?>"/>
					</div>

          <!-- Field for Email -->
					<div class="form-group">
						<input placeholder="Email" type="email" name="email" class="form-control" value="<?php if(isset($_POST['send_data'])){echo $_POST["email"];}?>"/>
					</div>

          <!-- Part-time or Full-time Radio Buttons -->
          <div class="form-check">
            <p class="lead">Please select one option below</p>
            <label class="form-check-label">
              <input class="form-check-input" type="radio" name="hours" id="hours" value="part_time" checked>
              <p>Part-time</p>
            </label>
          </div>
          <div class="form-check">
            <label class="form-check-label">
              <input class="form-check-input" type="radio" name="hours" id="hours" value="full_time">
              <p>Full-time</p>
            </label>
          </div>

          <!-- Section to Upload PDF -->
					<div class="form-group">
            <legend class="lead">Please include the pdf form below</legend>
						<input type="file" name="file_to_upload" id="file_to_upload">
					</div>

          <!-- Submit Form Button -->
					<div class="form-group">
						<input class="btn btn-primary btn-md" type="submit" name="send_data" class="form-control" value="Submit Information"/>
					</div>
				</form>
        <hr>
			</div>

		</div>

    <!-- jQuery CDN -->
		<script
		  src="http://code.jquery.com/jquery-3.2.1.min.js"
		  integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4="
		  crossorigin="anonymous"></script>

    <!-- Personalized JavaScript -->
		<script src="app.js"></script>
	</body>
</html>
