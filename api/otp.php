<?php

require "../vendor/autoload.php";

use OTPHP\TOTP;

// A random secret will be generated from this.
// You should store the secret with the user for verification.

function storingHash()
{
    $otp = TOTP::generate();
    $otp_hash_in_db = "ELXNZCRFPWPQUVR4C5GYXNETLYX4QSE37XOUDMZ7ZILYQKSLZVGWEKTNTF7OSF6NUVF6RG2IWJQDO3ZZOIHF72M25I77BMHV3HOHOBY";
    return $otp_hash_in_db;
}

function qrScanner($hash)
{
    $otp = TOTP::createFromSecret($hash);
    $otp->setLabel('proba');
    $grCodeUri = $otp->getQrCodeUri(
        'https://api.qrserver.com/v1/create-qr-code/?data=[DATA]&size=300x300&ecc=M',
        '[DATA]'
    );
    return $grCodeUri;
}

function otpValidation($secret, $input)
{
    $otp = TOTP::createFromSecret($secret);
    return $otp->verify($input);
}

// Generate OTP hash
$hash = storingHash();

// Create QR code URL
$qrCodeUrl = qrScanner($hash);

// Perform OTP validation
$input_otp = TOTP::createFromSecret($hash)->now();
$isValid = otpValidation($hash, $input_otp);

// Return JSON response
$response = [
    'secret' => $hash,
    'qrCodeUrl' => $qrCodeUrl,
    'isValid' => $isValid
];

header('Content-Type: application/json');
echo json_encode($response);
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Authentication</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="wrapper">
        <h2>OTP Authentication</h2>
        <div>
            <div id="qrcode"></div>
        </div>
        <div class="input-box">
            <input type="text" name="code" id="code" placeholder="Enter code">
        </div>
        <div class="error-message" id="errorMessage"></div>
        <div class="input-box button">
            <input type="submit" value="Verify" onclick="validateOTP()">
        </div>
    </div>

    <style>
        .error-message {
            color: red;
            font-size: 14px;
            margin-bottom: 3px;
            text-align: center;
        }
    </style>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/qrcode-generator@1.4.1/dist/qrcode.min.js"></script>
    <script>
        // Generate and display the QR code
            var secret = '';
            var qrCodeUrl = '';

            <?php
            $secret = $hash;
            //$qrCodeUrl = $qrCodeUrl;
            ?>

            var qrCodeContainer = document.getElementById('qrcode');
            var qrCodeImg = document.createElement('img');
            qrCodeImg.src = '<?php echo $qrCodeUrl; ?>';
            qrCodeContainer.appendChild(qrCodeImg);

            window.validateOTP = function() {
                var code = $('#code').val();

                if (code.trim() === '') {
                    $('#errorMessage').text('Please enter the verification code.');
                    return;
                }

                var isValid = response.isValid;
                if (isValid) {
                    $('#errorMessage').text('OTP is valid');
                    window.location.href = '../front/login.html'; // Redirect to login.html
                } else {
                    $('#errorMessage').text('OTP is invalid');
                }
            }
        
        
    </script>
</body>

</html>
