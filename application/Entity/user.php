<?php

namespace App\Entity;

class User
{
    private $id;

    private $username;

    private $password;

    private $passwordhash;

    private $email;

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->passwordhash = password_hash($password, CRYPT_BLOWFISH);
    }

    public function getPasswordHash()
    {
        return $this->passwordhash;
    }
}
