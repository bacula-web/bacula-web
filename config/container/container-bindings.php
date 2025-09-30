<?php

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

declare(strict_types=1);

use App\Command\SetupAuthCommand;
use App\CsrfErrorHandler;
use App\Libs\Config;
use App\Libs\FileConfig;
use App\Libs\PhpFileConfig;
use App\Table\CatalogTable;
use App\Table\ClientTable;
use App\Table\JobFileTable;
use App\Table\JobTable;
use App\Table\LogTable;
use App\Table\PoolTable;
use App\Table\UserTable;
use App\Table\VolumeTable;
use Core\Db\DatabaseFactory;
use Core\Db\ManagerRegistry;
use Core\Twig\Extension\TransformBytes;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Tools\DsnParser;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use Odan\Session\PhpSession;
use Odan\Session\SessionInterface;
use Odan\Session\SessionManagerInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\Csrf\Guard;
use Slim\Factory\Psr17\GuzzlePsr17Factory;
use Slim\Views\Twig;
use Twig\Extension\DebugExtension;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\Loader\MoFileLoader;
use Symfony\Bridge\Twig\Extension\TranslationExtension;
use function DI\factory;

return ['settings' => [
    'doctrine' => [
        'config' => [
            'isDevMode' => true,
            'paths' =>
                    BW_ROOT . '/application/Entity'

        ],
        'connection.core' => 'pdo-sqlite:///' . BW_ROOT . '/application/assets/protected/application.db',
        ],
    'session' => [
        'name' => $_ENV['APP_NAME'],
        'lifetime' => 7200,
        'path' => null,
        'domain' => null,
        'secure' => true,
        'httponly' => true,
        'cache_limiter' => 'nocache',
        'cookie_samesite' => 'Lax'
    ],
    'config_file' => CONFIG_FILE],

    ResponseFactoryInterface::class => GuzzlePsr17Factory::getResponseFactory(),

    'csrf' => function (ResponseFactoryInterface $responseFactory, CsrfErrorHandler $csrf) {
        return new Guard($responseFactory, failureHandler: $csrf->handle($responseFactory), persistentTokenMode: true);
    },

    JobTable::class => function (SessionInterface $session) {
        return new JobTable(DatabaseFactory::getDatabase($session->get('catalog_id', 0)));
    }, PoolTable::class => function (SessionInterface $session) {
        return new PoolTable(DatabaseFactory::getDatabase($session->get('catalog_id', 0)));
    }, ClientTable::class => function (SessionInterface $session) {
        return new ClientTable(DatabaseFactory::getDatabase($session->get('catalog_id', 0)));
    }, VolumeTable::class => function (SessionInterface $session) {
        return new VolumeTable(DatabaseFactory::getDatabase($session->get('catalog_id', 0)));
    }, JobFileTable::class => function (SessionInterface $session, ContainerInterface $container) {
        return new JobFileTable(
            DatabaseFactory::getDatabase($session->get('catalog_id', 0)),
            $container->get(CatalogTable::class)
        );
    }, CatalogTable::class => function (SessionInterface $session) {
        return new CatalogTable(DatabaseFactory::getDatabase($session->get('catalog_id', 0)));
    }, UserTable::class => function () {
        return new UserTable(DatabaseFactory::getDatabase());
    }, LogTable::class => function (SessionInterface $session) {
        return new LogTable(DatabaseFactory::getDatabase($session->get('catalog_id', 0)));
    }, SessionManagerInterface::class => function (ContainerInterface $container) {
        return $container->get(SessionInterface::class);
    }, SessionInterface::class => function (ContainerInterface $container) {
        $options = $container->get('settings')['session'];
        return new PhpSession($options);
    }, Twig::class => function (
        ContainerInterface $container,
        SessionInterface $session,
        Config $config
) {
        /**
         * TODO: cache must be set to path for prod env, or false for dev env, strict_variables and debug must be set to true only in dev env
         */
        $twig = Twig::create(TPL_DIR, [
            'cache' => false,
            'strict_variables' => false,
            'debug' => true]);

        $twig->addExtension(new DebugExtension());
        $twig->addExtension(new TransformBytes());

        $twig->getEnvironment()->addGlobal('app_name', $_ENV['APP_NAME']);
        $twig->getEnvironment()->addGlobal('app_version', $_ENV['APP_VERSION']);
        $twig->getEnvironment()->addGlobal('app_datetime_format', $config->get('datetime_format', 'Y-m-d H:i:s'));
        $twig->getEnvironment()->addGlobal('app_datetime_format_short', $config->get('datetime_format_short', 'Y-m-d'));

        $getLabels = function ($array) {
            $list = [];
            foreach ($array as $key => $value) {
                $list[$key] = $value['label'];
            }
            return $list;
        };

        $catalogsList = $config->getArrays();
        $twig->getEnvironment()->addGlobal(
            'catalogs',
            $getLabels($catalogsList)
        );

        $twig->getEnvironment()->addGlobal(
            'catalog_label',
            $catalogsList[$session->get('catalog_current_id', 0)]['label']
        );

        $twig->getEnvironment()->addGlobal('enable_users_auth', $config->get('enable_users_auth', true));

        $twig->getEnvironment()->addGlobal(
            'language',
            str_replace('_', '-', $config->get('language', 'en_US'))
        );

        $translator = $container->get(Translator::class);
        $twig->addExtension(new TranslationExtension($translator));

        return $twig;
    }, Translator::class => function (ContainerInterface $container) {
        $translator = new Translator('en_US');

        $locale = $container->get(Config::class)->get('language', 'en_US');

        $translator->addLoader('mo', new MoFileLoader());
        $translator->setLocale($locale);
        $translator->setFallbackLocales(['en_US']);

        $translationFile = LOCALE_DIR . "/$locale/LC_MESSAGES/messages.mo";
        $translator->addResource('mo', $translationFile, $locale);

        return $translator;
    }, Config::class => function (ContainerInterface $container) {
        $configFile = $container->get('settings')['config_file'];
        return new Config(PhpFileConfig::load($configFile));
    }, Connection::class => function (ContainerInterface $container) {
        $doctrineConfig = $container->get('settings')['doctrine'];
        $dsnParser = new DsnParser();
        $connectionParams = $dsnParser
            ->parse($doctrineConfig['connection.core']);
        $config = ORMSetup::createAttributeMetadataConfiguration($doctrineConfig['config']);
        return DriverManager::getConnection($connectionParams, $config);
    }, 'doctrine.em.default' => function (ContainerInterface $container) {
        $config = ORMSetup::createAttributeMetadataConfiguration([BW_ROOT . '/application/Entity/'], true);
        return new EntityManager($container->get(Connection::class), $config);
    }, 'doctrine.em.bacula' => function (ContainerInterface $container, SessionInterface $session) {
        FileConfig::open(CONFIG_FILE);
        $catalogId = $session->get('catalog_id', 0);

        if (FileConfig::get_Value('db_type', $catalogId) == 'sqlite') {
            $dsnParser = new DsnParser();
            $connectionParams = $dsnParser
                ->parse('pdo-sqlite:///' . FileConfig::get_Value('db_name', $catalogId));
            $connection = DriverManager::getConnection($connectionParams);
        } else {
            $connection = DriverManager::getConnection([
                'driver' => 'pdo_' . FileConfig::get_Value('db_type', $catalogId),
                'dbname' => FileConfig::get_Value('db_name', $catalogId),
                'user' => FileConfig::get_Value('login', $catalogId),
                'password' => FileConfig::get_Value('password', $catalogId),
                'host' => FileConfig::get_Value('host', $catalogId)
            ]);
        }

        return new EntityManager(
            $connection,
            ORMSetup::createAttributeMetadataConfiguration(
                [BW_ROOT . '/application/Entity/Bacula'],
                true
            )
        );
    },
    EntityManager::class => function (ContainerInterface $container) {
        return new EntityManager(
            $container->get(Connection::class),
            ORMSetup::createAttributeMetadataConfiguration([BW_ROOT . '/application/Entity/Core'], true
        ));
    },
    ManagerRegistry::class => factory(function(ContainerInterface $container){
       $connections = [
           'default' => 'doctrine.connection.default',
           'bacula' => 'doctrine.connection.bacula'
       ];
       $managers = [
           'default' => 'doctrine.em.default',
           'bacula' => 'doctrine.em.bacula'
       ];
        return new ManagerRegistry('ORM', $connections, $managers, 'default', 'default', '', $container);
    }),
    SetupAuthCommand::class => factory(function (ContainerInterface $container){
        return new SetupAuthCommand(null, $container->get(ManagerRegistry::class));
    }),
    ];
