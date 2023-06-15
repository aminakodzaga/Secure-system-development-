<?php
//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require '../config_default.php';

$env = parse_ini_file('../.env');
//Load Composer's autoloader
require '../vendor/autoload.php';

//Create an instance; passing `true` enables exceptions
$mail = new PHPMailer(true);

try {
    //Server settings
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = SMTP_HOST;                     //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username   = SMTP_USERNAME;                     //SMTP username
    $mail->Password   = SMTP_PASSWORD;                               //SMTP password
    $mail->SMTPSecure = SMTP_ENCRIPTION;            //Enable implicit TLS encryption
    $mail->Port       = SMTP_PORT;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

    //Recipients
    $mail->setFrom('info@securelogin.com', 'Secure Login');
    $mail->addAddress($_POST['email']);  // Add a recipient
   // $mail->addAddress('ellen@example.com');               //Name is optional
   // $mail->addReplyTo('info@example.com', 'Information');
   // $mail->addCC('cc@example.com');
   // $mail->addBCC('bcc@example.com');

    //Attachments
   // $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
   // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

    //Content
    $resetLink = 'https://local.com/front/reset-password.html';  // Change this to your reset password page URL

    $mail->isHTML(true);                                  //Set email format to HTML
    $mail->Subject = 'Password Reset Request';
    $mail->Body = 'Click the following link to reset your password: <a href="' . $resetLink . '">' . $resetLink . '</a>';
    $mail->AltBody = 'Please click the following link to reset your password: ' . $resetLink;

    $mail->send();
    $returnMessage = 'Message has been sent';
} catch (Exception $e) {
    $returnMessage = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}

?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Forgot Password Form </title>
    <link rel="stylesheet" href="../front/style.css">
   </head>
<body>
  <div class="wrapper">
    <h2>Forgot password</h2>
    <form action="../api/phpmailer.php" method="POST" onsubmit="return validateForm()">
        <div class="input-box">
          <div id="returnMessage"></div>
        </div>
        <div class="text">
          <h3><a href="../front/login.html">Go back to login</a></h3>
        </div>
      </form>
  </div>

  <style>
    .error-message {
  color: red;
  font-size: 14px;
  margin-bottom: 3px;
  text-align: center;
}

  </style>

<script>
  function validateForm() {
    var message = document.getElementById("returnMessage");
    message.textContent = ""; // Clear the previous message

    // Add your validation logic here

    // After successful validation
    message.textContent = "Sending email...";

    // Make an AJAX request to submit the form data
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "../api/phpmailer.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function () {
      if (xhr.readyState === XMLHttpRequest.DONE) {
        if (xhr.status === 200) {
          var responseText = xhr.responseText;
          var lines = responseText.split('\n');
          var lastMessage = lines[lines.length - 2]; // Get the second-to-last line
          message.textContent = lastMessage.trim();
        } else {
          message.textContent = "Error: " + xhr.responseText;
        }
      }
    };

    // Prepare the form data
    var formData = new FormData();
    formData.append("email", document.getElementById("email").value);

    // Send the request
    xhr.send(formData);

    return false; // Prevent form submission
  }
</script>


</body>
</html>
