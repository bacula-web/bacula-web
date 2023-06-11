<?php

declare(strict_types=1);

use App\Libs\FileConfig;
use App\Tables\ClientTable;
use App\Tables\JobFileTable;
use App\Tables\JobTable;
use App\Tables\LogTable;
use App\Tables\PoolTable;
use App\Tables\VolumeTable;
use Core\App\View;
use Core\Db\DatabaseFactory;
use Core\i18n\CTranslation;
use Odan\Session\PhpSession;
use Odan\Session\SessionInterface;
use Odan\Session\SessionManagerInterface;
use Psr\Container\ContainerInterface;

return [
    'settings' => [
        'session' => [
            'name' => $_ENV['APP_NAME'],
            'lifetime' => 7200,
            'path' => null,
            'domain' => null,
            'secure' => false,
            'httponly' => true,
            'cache_limiter' => 'nocache',
            'cookie_samesite' => 'Lax'
        ]
    ],
    JobTable::class => function(SessionInterface $session) {
      return new JobTable(DatabaseFactory::getDatabase($session->get('catalog_id', 0)));
    },
    PoolTable::class => function(SessionInterface $session) {
        return new PoolTable(DatabaseFactory::getDatabase($session->get('catalog_id', 0)));
    },
    ClientTable::class => function(SessionInterface $session) {
        return new ClientTable(DatabaseFactory::getDatabase($session->get('catalog_id', 0)));
    },
    VolumeTable::class => function(SessionInterface $session) {
        return new VolumeTable(DatabaseFactory::getDatabase($session->get('catalog_id', 0)));
    },
    JobFileTable::class => function(SessionInterface $session) {
        return new JobFileTable(DatabaseFactory::getDatabase($session->get('catalog_id', 0)));
    },
    LogTable::class => function(SessionInterface $session) {
        return new LogTable(DatabaseFactory::getDatabase($session->get('catalog_id', 0)));
    },
    View::class => function(Odan\Session\PhpSession $session) {
        $view = new View();
        $view->set('app_name', $_ENV['APP_NAME']);
        $view->set('app_version', $_ENV['APP_VERSION']);
        $view->set('user_authenticated', true);
        $view->set('enable_users_auth', ((FileConfig::get_Value('enable_users_auth') !== null) && is_bool(FileConfig::get_Value('enable_users_auth'))) ? (bool)FileConfig::get_Value('enable_users_auth') : true);

        $view->set('catalog_label', FileConfig::get_Value('label', 0));
        $view->set('catalogs', FileConfig::get_Catalogs());
        $view->set('catalog_current_id', 0);

        $view->set('username', $session->get('username'));

        $language = FileConfig::get_Value('language');
        $translate = new CTranslation($language);
        $translate->setLanguage();
        $view->set('language', str_replace('_', '-', $language));

        return $view;
    },
    SessionManagerInterface::class => function (ContainerInterface $container) {
        return $container->get(SessionInterface::class);
    },

    SessionInterface::class => function (ContainerInterface $container) {
        $options = $container->get('settings')['session'];

        return new PhpSession($options);
    }
];
