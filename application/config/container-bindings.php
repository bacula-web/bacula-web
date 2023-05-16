<?php

declare(strict_types=1);

use App\Tables\ClientTable;
use App\Tables\JobFileTable;
use App\Tables\JobTable;
use App\Tables\LogTable;
use App\Tables\PoolTable;
use App\Tables\VolumeTable;
use Core\Db\DatabaseFactory;
use Symfony\Component\HttpFoundation\Request;

return [
    JobTable::class => function(Symfony\Component\HttpFoundation\Session\Session $session) {
      return new JobTable(DatabaseFactory::getDatabase($session->get('catalog_id', 0)));
    },
    PoolTable::class => function(Symfony\Component\HttpFoundation\Session\Session $session) {
        return new PoolTable(DatabaseFactory::getDatabase($session->get('catalog_id', 0)));
    },
    ClientTable::class => function(Symfony\Component\HttpFoundation\Session\Session $session) {
        return new ClientTable(DatabaseFactory::getDatabase($session->get('catalog_id', 0)));
    },
    VolumeTable::class => function(Symfony\Component\HttpFoundation\Session\Session $session) {
        return new VolumeTable(DatabaseFactory::getDatabase($session->get('catalog_id', 0)));
    },
    JobFileTable::class => function(Symfony\Component\HttpFoundation\Session\Session $session) {
        return new JobFileTable(DatabaseFactory::getDatabase($session->get('catalog_id', 0)));
    },
    LogTable::class => function(Symfony\Component\HttpFoundation\Session\Session $session) {
        return new LogTable(DatabaseFactory::getDatabase($session->get('catalog_id', 0)));
    },
    Request::class => Request::createFromGlobals()
];
