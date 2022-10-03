<?php

namespace App\Entity;

use Symfony\Component\HttpFoundation\Session\Session;

class Log
{

    private $logtext;

    private $time;


    public function getLogText()
    {
        return nl2br($this->logtext);
    }

    public function getTime()
    {
        // TODO: we may not need to store everything in the session
        $session = new Session();
        return date($session->get('datetime_format'), strtotime($this->time));
    }
}
