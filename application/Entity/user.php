<?php

namespace App\Entity;

class User {

    private $id;

    private $username;

    private $password;

    private $hashedPassword;

    private $email;

    public function getUsername()
    {
        return $this->username;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->hashedPassword = password_hash($password, CRYPT_BLOWFISH);
    }

    public function getHashedPassword()
    {
        return $this->hashedPassword;
    }
}
