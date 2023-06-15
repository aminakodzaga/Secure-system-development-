<?php
require '../vendor/autoload.php';

$phone = $_GET['phone'];
$phoneUtil = \libphonenumber\PhoneNumberUtil::getInstance();
try {
    $swissNumberProto = $phoneUtil->parse($phone, "BA");

    $type = $phoneUtil->getNumberType($swissNumberProto);
    print_r($type); 
    
} catch (\libphonenumber\NumberParseException $e) {
    var_dump($e);
}
?>