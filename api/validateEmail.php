<?php
require '../vendor/autoload.php';

$email = $_GET['email'];


 $split = explode("@", $email);
    $hostname = $split[1];
    getmxrr($hostname, $hosts);
    
    //print_r($hosts);
    
    if((is_array($hosts) && count($hosts) == 0) || $hosts == null){
        echo "No MX records found for $hostname";
    }   else echo 'ok';  


// Check if the request is made through AJAX
if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
    // Check if the 'email' parameter is present in the request
    if(isset($_GET['email'])) {
        $email = $_GET['email'];
        $result = validateEmail($email);
        echo $result;
    }
}

?>