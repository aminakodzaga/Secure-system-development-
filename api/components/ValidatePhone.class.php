<?php
require '../vendor/autoload.php';

class ValidatePhone {
    function check_phone($phone) {
        $phoneUtil = \libphonenumber\PhoneNumberUtil::getInstance();
        try {
            $parsedPhone = $phoneUtil->parse($phone, "BA");
            var_dump($parsedPhone);
        } catch (\libphonenumber\NumberParseException $e) {
            var_dump($e);
        }
        $isValid = $phoneUtil->isValidNumber($parsedPhone);
        return $isValid;
    }
}

?>
