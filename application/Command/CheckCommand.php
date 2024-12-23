<?php

declare(strict_types=1);

/**
 * Copyright (C) 2010-present Davide Franco
 *
 * This file is part of the Bacula-Web project.
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

use App\Service\AppCheck;
use PDO;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * Check server requirements from the console
 */
class CheckCommand extends Command
{
    /**
     * @var AppCheck
     */
    private AppCheck $appCheck;

    /**
     * @var ParameterBagInterface
     */
    private ParameterBagInterface $parameters;

    /**
     * @param AppCheck $appCheck
     * @param ParameterBagInterface $parameters
     */
    public function __construct(AppCheck $appCheck, ParameterBagInterface $parameters)
    {
        parent::__construct();

        $this->appCheck = $appCheck;
        $this->parameters = $parameters;
    }

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this->setDescription('Check requirements')
            ->setHelp('Check requirements such as PHP version, installed PHP extensions, etc.')
            ->setName('app:check');

        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
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
        $phpVersionCheck = $this->appCheck->checkPhpVersion();

        $output->writeln(
            [
                'PHP version',
                '===========',
                ''
            ]);

        if ($phpVersionCheck['result']) {
            $errorMessages = ['PHP version -> ok'];
            $formattedBlock = $formatter->formatBlock($errorMessages, 'info');
        } else {
            $errorMessages = [
                'Wrong PHP version',
                'You have to upgrade PHP to at least version' . $this->parameters->get('app.min_php_version')
                ];
            $formattedBlock = $formatter->formatBlock($errorMessages, 'error');
        }
        $output->writeln($formattedBlock);
        $output->writeln([
            '[Info] Current version is ' . PHP_VERSION
        ]);

        // Check PHP timezone
        $output->writeln(
            [
                'PHP timezone',
                '============',
                ''
            ]);

        $timezone = ini_get('date.timezone');
        $timezoneCheck = $this->appCheck->checkTimezone();

        if ($timezoneCheck['result']) {
            $errorMessages = ['PHP timezone -> ok'];
            $formattedBlock = $formatter->formatBlock($errorMessages, 'info');
        } else {
            $timezone = '[empty]';
            $errorMessages = ['PHP timezone not set', 'PHP timezone is not configured in php.ini'];
            $formattedBlock = $formatter->formatBlock($errorMessages, 'error');
        }
        $output->writeln($formattedBlock);
        $output->writeln(["[Info] Current PHP timezone is $timezone", '']);

        // Check assets folder permissions
        $output->writeln(
            [
                'Cache folder is writable',
                '===================================',
                ''
            ]);

        $cacheFolderCheck = $this->appCheck->checkCacheDirIsWritable();
        $cacheFolder = $this->parameters->get('kernel.cache_dir');

        if ($cacheFolderCheck['result']) {
            $errorMessages = ['Cache folder iw writable -> ok'];
            $formattedBlock = $formatter->formatBlock($errorMessages, 'info');
        } else {
            $errorMessages = ['Cache folder iw writable -> error'];
            $formattedBlock = $formatter->formatBlock($errorMessages, 'error');
        }
        $output->writeln($formattedBlock);
        $output->writeln([
            "[Info] Folder <options=bold>$cacheFolder</> must be writable by web server user"
        ]);

        // List available PHP PDO drivers
        $output->writeln(
            [
                'Checking installed PHP PDO available drivers',
                '============================================',
                ''
            ]);

        foreach (PDO::getAvailableDrivers() as $driver) {
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
        if ($this->appCheck->hasGettextExtension()) {
            $output->writeln('Gettext support -> <info>Ok</info>');
        } else {
            $output->writeln('Gettext support -> <error>Error</error>');
        }
        $output->writeln(['[Info] PHP Gettext extension must be installed', '']);

        // Check PHP Session support
        if ($this->appCheck->hasSessionExtension()) {
            $output->writeln('PHP Session support -> <info>Ok</info>');
        } else {
            $output->writeln('PHP Session support -> <error>Error</error>');
        }
        $output->writeln(['[Info] PHP Session extension must be installed', '']);


        // Check PHP PDO support
        if ($this->appCheck->hasPdoExtension()) {
            $output->writeln('PHP PDO support -> <info>Ok</info>');
        } else {
            $output->writeln('PHP PDO support -> <error>Error</error>');
        }
        $output->writeln(['[Info] PHP PDO extension must be installed', '']);

        return Command::SUCCESS;
    }
}
