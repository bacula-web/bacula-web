<?php

namespace App\Entity;

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
        return date($_SESSION['datetime_format'], strtotime($this->time));
    }
}
