<?php

class ValidatePassword {

  function check_password($password) {

    $hash_password = strtoupper(sha1($password));
    $first_5_chars = substr($hash_password, 0, 5);
    $other_chars = substr($hash_password, 5);
    $response = file_get_contents("https://api.pwnedpasswords.com/range/" . $first_5_chars);

    if (strpos($response, $other_chars) !== false) {
      return true;
    } else {
      return false;
    }
  }
}

?>
