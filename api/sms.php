<?php

require_once(__DIR__ . '/../config_default.php');

$number = $_GET['phone'];
$code = $_GET['code'];
$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, 'https://rest.nexmo.com/sms/json');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, "from=Mirza&text=".$code."&to=".$number."&api_key=".TEXT_MESSAGE_API_KEY."&api_secret=".TEXT_MESSAGE_SECRET."");

$headers = array();
$headers[] = 'Content-Type: application/x-www-form-urlencoded';
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch);
if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
}
curl_close($ch);




?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> SMS Confirmation </title>
    <link rel="stylesheet" href="../front/style.css">
<body>
  <div class="wrapper">
    <h2>SMS Confirmation</h2>
    <form action="#" method="POST" onsubmit="return validateForm()">     
      <div class="input-box">
        <input type="text" name="code" id="code" placeholder="Enter code">
      </div>
      <div class="error-message" id="errorMessage"></div>
      <div class="input-box button">
        <input type="submit" value="Verify">
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

<script src="https://cdn.jsdelivr.net/npm/qrcode-generator@1.4.1/dist/qrcode.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>

    function validateForm() {
      const urlParams = new URLSearchParams(window.location.search);
      var formData = Object.fromEntries(urlParams.entries());
      const phoneNumber = urlParams.get('phone');
      const generateCode = urlParams.get('code');      
      var code = document.getElementById('code').value;

      document.getElementById('errorMessage').textContent = '';
      console.log(formData);
      if (code.trim() === '') {
        document.getElementById('errorMessage').textContent = 'Please enter the verification code.';
        return false;
      }

      if (code !== generateCode) {
        document.getElementById('errorMessage').textContent = 'Incorrect verification code.';
        return false;
      } else {
        $.ajax({
          url: "../api/register",
          type: "POST",
          data: JSON.stringify(formData),
          contentType: "application/json",
          dataType: "json",
          success: function(response) {
            // Redirect to login.html on success
            window.location.href = '../front/login.html';

          },
          error: function(jqXHR, textStatus, errorThrown) {
            var errorMessage;
            if (jqXHR.responseJSON && jqXHR.responseJSON.message) {
              errorMessage = jqXHR.responseJSON.message;
            } else {
              errorMessage = "An error occurred.";
            }
            $('#errorMessage').text(errorMessage);
          }
        });
        
        return false; // Prevent form submission
      }



      return true;
    }
</script>


</body>

</html>
