<?php

require_once __DIR__.'/../vendor/autoload.php';
require '../config_default.php';
require_once dirname(__FILE__) . "/components/ValidatePassword.class.php";
require_once dirname(__FILE__) . "/components/ValidateEmail.class.php";
require_once dirname(__FILE__) . "/components/ValidatePhone.class.php";


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use OpenApi\Annotations as OA;


require_once __DIR__.'/UserDao.class.php';

Flight::register('userDao', 'UserDao');

/**
 * @OA\Tag(
 *     name="user",
 *     description="User related operations"
 * )
 * @OA\Info(
 *     version="1.0",
 *     title="Example for response examples value",
 *     description="Example info",
 *     @OA\Contact(name="Swagger API Team")
 * )
 * @OA\Server(
 *     url="../api",
 *     description="API server"
 * )
 */

 function handleException(Exception $ex) {
    // Handle the exception here
    // You can log the error or return an error response

    // Example:
    Flight::json(array(
        'status' => 'error',
        'message' => 'An error occurred: ' . $ex->getMessage()
    ));
}

// Set the exception handler
Flight::set('flight.log_errors', false);
Flight::set('flight.handle_errors', false);
set_exception_handler('handleException');


/**
 * @OA\Post(
 *     path="/login",
 *     summary="Adds a new user - with oneOf examples",
 *     description="Login",
 *     operationId="addUser",
 *     tags={"user"},
 *     @OA\RequestBody(
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 @OA\Property(
 *                     property="username",
 *                     type="string"
 *                 ),
 *                 @OA\Property(
 *                     property="password",
 *                     type="string"
 *                 ),
 
 *                 example={"username": "Benjamin", "password": "ha10be14"}
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="OK",
 *         @OA\JsonContent(
 *             oneOf={
 *                 @OA\Schema(type="boolean")
 *             },
 *             @OA\Examples(example="result", value={"success": true}, summary="An result object."),
 *             @OA\Examples(example="bool", value=false, summary="A boolean value."),
 *         )
 *     )
 * )
 */

 Flight::route('POST /login', function () {
    $login = Flight::request()->data->getData();

    $user = Flight::userDao()->get_user_by_username($login['username']);

    
    if (isset($user['idusers'])) {
        // Use a secure hashing algorithm to verify the password
        if($user['password'] == md5($login['password'])) {
            unset($user['password']);
            $jwt = JWT::encode($user, JWT_SECRET, 'HS256');
            Flight::json(['token' => $jwt]);
        } else {
            Flight::json(["message" => "Password incorrect"], 404);
        }

        if (!isset($login['username']) || !isset($login['password'])) {
            Flight::json([
                'status' => 'error',
                'message' => 'Both username and password are required.'
            ]);
            die;
        }

        if (!preg_match('/^[A-Za-z0-9]{3,}$/', $login['username'])) {
            Flight::json([
                'status' => 'error',
                'message' => 'Invalid username format. The username should be longer than 8 characters, contain only alphanumeric characters and no spaces.'
            ]);
            die;
        }

        if (strlen($login['password']) <= 8) {
            Flight::json([
                'status' => 'error',
                'message' => 'The password should be longer than 8 characters.'
            ]);
            die;
        }

        $verifier = new ValidatePassword();
        $isPasswordBreached = $verifier->check_password($login['password']);
        if ($isPasswordBreached) {
            Flight::json([
                'status' => 'error',
                'message' => 'The password is not secure.'
            ]);
            die;
        }
      }
        
});


  /**
 * @OA\Post(
 *     path="/register",
 *     summary="register user",
 *     description="Adds a new user",
 *     operationId="addUser",
 *     tags={"user"},
 *     @OA\RequestBody(
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 @OA\Property(
 *                     property="username",
 *                     type="string"
 *                 ),
 *                 @OA\Property(
 *                     property="password",
 *                     type="string"
 *                 ),
 *                @OA\Property(
 *                     property="email",
 *                     type="string"
 *                 ),
 *                 @OA\Property(
 *                     property="phone",
 *                     oneOf={
 *                         @OA\Schema(type="string"),
 *                         @OA\Schema(type="integer"),
 *                     }
 *                 ),
 *                 example={"username": "Benjamin", "password": "ha10be14", "email": "bmehanovic12@gmail.com", "phone": 12345678}
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="OK",
 *         @OA\JsonContent(
 *             oneOf={
 *                 @OA\Schema(type="boolean")
 *             },
 *             @OA\Examples(example="result", value={"success": true}, summary="An result object."),
 *             @OA\Examples(example="bool", value=false, summary="A boolean value."),
 *         )
 *     )
 * )
 */
Flight::route('POST /register', function () {
    $fullName = Flight::request()->data->fullName;
    $username = Flight::request()->data->username;
    $password = Flight::request()->data->password;
    $password_conf = Flight::request()->data->password_conf;
    $email = Flight::request()->data->email;
    $phoneNumber = Flight::request()->data->phoneNumber;
    $mfa = Flight::request()->data->mfa;

    if (!preg_match('/^[A-Za-z0-9]{3,}$/', $username)) {
      Flight::json(array(
        'status' => 'error',
        'message' => 'Invalid username format. The username should be longer than 8 characters, contain only alphanumeric characters and no spaces.'
      ));
      die;
    }

    if (strlen($password) <= 8) {
      Flight::json(array(
        'status' => 'error',
        'message' => 'The password should be longer than 8 characters.'
      ));
      die;
    }

    if ($password != $password_conf) {
      Flight::json(array(
        'status' => 'error',
        'message' => 'The passwords do not match.'
      ));
      die;
    }

    $verifier = new ValidatePassword();
    $isPasswordBreached = $verifier -> check_password($password);
    if ($isPasswordBreached) {
      Flight::json(array(
        'status' => 'error',
        'message' => 'The password is not secure.'
      ));
      die;
    }

    $phoneVerifier = new ValidateEmail();
    $isEmailValid = $phoneVerifier -> check_email($email);
    if (!$isEmailValid) {
      Flight::json(array(
        'status' => 'error',
        'message' => "The email is not valid."
      ));
      die;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      Flight::json(array(
        'status' => 'error',
        'message' => "The email address: '" . $email . "' is not valid."
      ));
      die;
    }

    $phoneVerifier = new ValidatePhone();
    $isPhoneValid = $phoneVerifier -> check_phone($phoneNumber);
    if (!$isPhoneValid) {
      Flight::json(array(
        'status' => 'error',
        'message' => "The phone number is not valid."
      ));
      die;
    }

    if (Flight::userDao()->get_user_by_username($username)) {
        Flight::json(["message" => "Username already registered"], 500);
    } else if (Flight::userDao()->get_user_by_email($email)) {
        Flight::json(["message" => "Email already registered"], 500);
    } else {
        Flight::json(Flight::userDao()->addUser($username, $fullName, $password, $email, $phoneNumber, $mfa));
    }
});
/**
 * @OA\Get(path="/user", tags={"user"}, security={{"ApiKeyAuth": {}}},
 *         summary="Return current logged in user id. ",
 *         @OA\Response( response=200, description="User id.")
 * )
 */
Flight::route('GET /user', function () {
    Flight::json(Flight::userDao()->get_user_by_username());
});

Flight::start();


?>
