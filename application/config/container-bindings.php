<?php

declare(strict_types=1);

/**
 * Copyright (C) 2010-present Davide Franco
 *
 * This file is part of Bacula-Web.
 *
 * Bacula-Web is free software: you can redistribute it and/or modify it under the terms of the GNU
 * General Public License as published by the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * Bacula-Web is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with Bacula-Web. If not, see
 * <https://www.gnu.org/licenses/>.
 */

use App\CsrfErrorHandler;
use App\Libs\FileConfig;
use App\Table\CatalogTable;
use App\Table\ClientTable;
use App\Table\JobFileTable;
use App\Table\JobTable;
use App\Table\LogTable;
use App\Table\PoolTable;
use App\Table\UserTable;
use App\Table\VolumeTable;
use Core\Db\DatabaseFactory;
use Odan\Session\PhpSession;
use Odan\Session\SessionInterface;
use Odan\Session\SessionManagerInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\App;
use Slim\Csrf\Guard;
use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Twig\Extension\DebugExtension;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\Loader\MoFileLoader;
use Symfony\Bridge\Twig\Extension\TranslationExtension;

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
    App::class => function (ContainerInterface $container) {
        AppFactory::setContainer($container);
        return AppFactory::create();
    },
    ResponseFactoryInterface::class => function (App $app) {
        return $app->getResponseFactory();
    },
    'csrf' => function(ResponseFactoryInterface $responseFactory, CsrfErrorHandler $csrf) {
        return new Guard(
            $responseFactory,
            failureHandler: $csrf->handle($responseFactory),
            persistentTokenMode: false);
    },
    JobTable::class => function (SessionInterface $session) {
        return new JobTable(DatabaseFactory::getDatabase($session->get('catalog_id', 0)));
    },
    PoolTable::class => function (SessionInterface $session) {
        return new PoolTable(DatabaseFactory::getDatabase($session->get('catalog_id', 0)));
    },
    ClientTable::class => function (SessionInterface $session) {
        return new ClientTable(DatabaseFactory::getDatabase($session->get('catalog_id', 0)));
    },
    VolumeTable::class => function (SessionInterface $session) {
        return new VolumeTable(DatabaseFactory::getDatabase($session->get('catalog_id', 0)));
    },
    JobFileTable::class => function (SessionInterface $session, ContainerInterface $container) {
        return new JobFileTable(
            DatabaseFactory::getDatabase($session->get('catalog_id', 0)),
            $container->get(CatalogTable::class));
    },
    CatalogTable::class => function (SessionInterface $session) {
        return new CatalogTable(DatabaseFactory::getDatabase($session->get('catalog_id', 0)));
    },
    UserTable::class => function () {
        return new UserTable(DatabaseFactory::getDatabase());
    },
    LogTable::class => function (SessionInterface $session) {
        return new LogTable(DatabaseFactory::getDatabase($session->get('catalog_id', 0)));
    },
    SessionManagerInterface::class => function (ContainerInterface $container) {
        return $container->get(SessionInterface::class);
    },
    SessionInterface::class => function (ContainerInterface $container) {
        $options = $container->get('settings')['session'];

        return new PhpSession($options);
    },
    Twig::class => function (ContainerInterface $container, SessionInterface $session) {
        $twig = Twig::create(BW_ROOT . '/application/views/templates', ['cache' => false]);

        $twig->addExtension(new DebugExtension());

        $twig->getEnvironment()->addGlobal('app_name', $_ENV['APP_NAME']);
        $twig->getEnvironment()->addGlobal('app_version', $_ENV['APP_VERSION']);

        FileConfig::open(CONFIG_FILE);
        $twig->getEnvironment()->addGlobal('catalogs', FileConfig::get_Catalogs());

        $twig->getEnvironment()->addGlobal(
            'catalog_label',
            FileConfig::get_Value('label', $session->get('catalog_current_id', 0)));

        $twig->getEnvironment()->addGlobal('enable_users_auth', FileConfig::get_Value('enable_users_auth'));
        $twig->getEnvironment()->addGlobal('language', str_replace('_', '-', FileConfig::get_Value('language')));

        $translator = $container->get(Translator::class);
        $twig->addExtension(new TranslationExtension($translator));

        return $twig;
    },
    Translator::class => function (ContainerInterface $container) {
        $translator = new Translator('en_US');

        $locale = FileConfig::get_Value('language');

        $translator->addLoader('mo', new MoFileLoader());
        $translator->setLocale($locale);
        $translator->setFallbackLocales(['en_US']);

        $translationFile = __DIR__ . "/../locale/$locale/LC_MESSAGES/messages.mo";
        $translator->addResource('mo', $translationFile, $locale);

        return $translator;
    }
];
