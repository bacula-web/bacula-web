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

namespace App\Command;

use PDO;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CheckCommand extends Command
{
    protected function configure(): void
    {
        $this->setDescription('Check requirements')
            ->setHelp('Check requirements such as PHP version, installed PHP extensions, etc.')
            ->setName('check');

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $formatter = $this->getHelper('formatter');

        $output->writeln(
            [
                'Checking Bacula-Web requirements',
                '================================',
                ''
            ]);

        // Check PHP version
        $phpversion = phpversion();

        $output->writeln(
            [
                'PHP version',
                '===========',
                ''
            ]);

        if (version_compare(PHP_VERSION, '8.0', '>=')) {
            $errorMessages = ['PHP version -> ok'];
            $formattedBlock = $formatter->formatBlock($errorMessages, 'info');
        } else {
            $errorMessages = ['Wrong PHP version', 'You have to upgrade PHP to at least version 8.0'];
            $formattedBlock = $formatter->formatBlock($errorMessages, 'error');
        }
        $output->writeln($formattedBlock);
        $output->writeln(["[Info] Current version is $phpversion", '']);

        // Check PHP timezone
        $output->writeln(
            [
                'PHP timezone',
                '============',
                ''
            ]);
        $timezone = ini_get('date.timezone');

        if (!empty($timezone)) {
            $errorMessages = ['PHP timezone -> ok'];
            $formattedBlock = $formatter->formatBlock($errorMessages, 'info');
        } else {
            $timezone = '<not set>';
            $errorMessages = ['PHP timezone not set', 'PHP timezone is not configured in php.ini'];
            $formattedBlock = $formatter->formatBlock($errorMessages, 'error');
        }
        $output->writeln($formattedBlock);
        $output->writeln(["[Info] Current PHP timezone is $timezone", '']);

        // Check assets folder permissions
        $output->writeln(
            [
                'Protected assets folder is writable',
                '===================================',
                ''
            ]);
        if (is_writable('application/assets/protected')) {
            $errorMessages = ['Protected assets folder iw writable -> ok'];
            $formattedBlock = $formatter->formatBlock($errorMessages, 'info');
        } else {
            $errorMessages = ['Protected assets folder iw writable -> error'];
            $formattedBlock = $formatter->formatBlock($errorMessages, 'error');
        }
        $output->writeln($formattedBlock);
        $output->writeln(['[Info] Folder <options=bold>application/assets/protected</> must be writable by web server user', '']);

        // Check Twig cache folder permissions
        $output->writeln(
            [
                'Twig cache folder is writable',
                '=============================',
                ''
            ]);
        if (is_writable(BW_ROOT . '/application/views/cache')) {
            $errorMessages = ['Twig cache folder write permission -> ok'];
            $formattedBlock = $formatter->formatBlock($errorMessages, 'info');
        } else {
            $errorMessages = ['Twig cache folder write permission -> error'];
            $formattedBlock = $formatter->formatBlock($errorMessages, 'info');
        }
        $output->writeln($formattedBlock);
        $output->writeln(['[Info] Folder <options=bold>/application/views/cache</> must be writable by web server user', '']);

        // List available PHP PDO drivers
        $output->writeln(
            [
                'Checking installed PHP database extensions',
                '==========================================',
                ''
            ]);

        foreach ($pdo_drivers = PDO::getAvailableDrivers() as $driver) {
            $output->writeln(["PDO $driver installed <info>Ok</info>", '']);
        }

        // Check PHP SQLite support
        $output->writeln(
            [
                'PHP extensions',
                '==============',
                ''
            ]);

        if (in_array('sqlite', PDO::getAvailableDrivers())) {
            $output->writeln('PHP PDO Sqlite extension -> <info>Ok</info>');
        } else {
            $output->writeln('PHP PDO Sqlite extension -> <error>Error</error>');
        }
        $output->writeln(['[Info] PHP PDO Sqlite extension must be installed', '']);

        // Check PHP Gettext support
        if (function_exists('gettext')) {
            $output->writeln('Gettext support -> <info>Ok</info>');
        } else {
            $output->writeln('Gettext support -> <error>Error</error>');
        }
        $output->writeln(['[Info] PHP Gettext extension must be installed', '']);

        // Check PHP Session support
        if (function_exists('session_start')) {
            $output->writeln('PHP Session support -> <info>Ok</info>');
        } else {
            $output->writeln('PHP Session support -> <error>Error</error>');
        }
        $output->writeln(['[Info] PHP Session extension must be installed', '']);

        // Check PHP PDO support
        if (class_exists('PDO')) {
            $output->writeln('PHP PDO support -> <info>Ok</info>');
        } else {
            $output->writeln('PHP PDO support -> <error>Error</error>');
        }
        $output->writeln(['[Info] PHP PDO extension must be installed', '']);

        // Check PHP Posix support
        if (function_exists('posix_getpwuid')) {
            $output->writeln('PHP Posix support -> <info>Ok</info>');
        } else {
            $output->writeln('PHP Posix support -> <error>Error</error>');
        }
        $output->writeln(['[Info] PHP Posix extension must be installed', '']);

        return Command::SUCCESS;
    }
}
