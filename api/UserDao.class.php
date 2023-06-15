<?php

require_once __DIR__.'/BaseDao.class.php';

class UserDao extends BaseDao
{
    /**
    * constructor of dao class
    */
    public function __construct()
    {
        parent::__construct("users");
    }



    public function addUser($username, $fullName, $password, $email, $phoneNumber, $mfa){
        // Rest of the code remains the same
        $password = md5($password);
        $this->query_unique(
            "INSERT INTO users (username, fullName, phoneNumber, email, password, mfa) VALUES (:username, :fullName, :phoneNumber, :email, :password, :mfa)",
            ['email' => $email, 'username' => $username, 'fullName' => $fullName, 'phoneNumber' => $phoneNumber,'password'=>$password, 'mfa'=>$mfa]
        );
    }
    public function get_user_by_username($username)
    {
        return $this->query_unique("SELECT * FROM users WHERE username = :username LIMIT 1", ['username' => $username]);
    }

    public function get_user_by_email($email)
    {
        return $this->query_unique("SELECT * FROM users WHERE email = :email LIMIT 1", ['email' => $email]);
    }
}
