<?php

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

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * This class provide helper methods to ensure the pre-requisites are satisfied
 * on the web server
 */
class AppCheck
{
    /**
     * @var array
     */
    private array $extensions;

    /**
     * @var ParameterBagInterface
     */
    private ParameterBagInterface $parameters;

    /**
     * @param ParameterBagInterface $parameters
     */
    public function __construct(ParameterBagInterface $parameters)
    {
        $this->extensions = get_loaded_extensions();
        $this->parameters = $parameters;
    }

    /**
     * Check if PHP Gettext extension is available
     *
     * @return array{label: string, description: string, result: bool}
     */
    public function checkGettextExtension(): array
    {
        return [
            'label' => 'PHP - Gettext support',
            'description' => 'If you want Bacula-web in your language, please compile PHP with Gettext support',
            'result' => $this->hasGettextExtension()
        ];
    }

    /**
     * @return array{label: string, description: string, result: bool}
     */
    public function checkSessionExtension(): array
    {
        return [
            'label' => 'PHP - Session support',
            'description' => 'PHP session support is required',
            'result' => $this->hasSessionExtension()
        ];
    }

    /**
     * @return array{label: string, description: string, result: bool}
     */
    public function checkSqliteExtension(): array
    {
        return [
            'label' => 'PHP - SQLite support',
            'description' => 'PHP SQLite support must be installed to use SQLite bacula catalog and for Bacula-Web back-end',
            'result' => in_array('pdo_sqlite', $this->extensions)
        ];
    }

    /**
     * @return array{label: string, description: string, result: bool}
     */
    public function checkMySqlExtension(): array
    {
        return [
            'label' => 'PHP - MySQL support',
            'description' => 'PHP MySQL support must be installed in order to run bacula-web with MySQL bacula catalog',
            'result' => in_array('pdo_mysql', $this->extensions)
        ];
    }

    /**
     * @return array{label: string, description: string, result: bool}
     */
    public function checkPostgresExtension(): array
    {
        return [
            'label' => 'PHP - PostgreSQL support',
            'description' => 'PHP PostgreSQL support must be installed in order to run bacula-web with PostgreSQL bacula catalog',
            'result' => in_array('pdo_pgsql', $this->extensions)
        ];
    }

    /**
     * @return array{label: string, description: string, result: bool}
     */
    public function checkPdoExtension(): array
    {
        return [
            'label' => 'PHP - PDO support',
            'description' => 'PHP PDO support is required, please compile PHP with this option',
            'result' => $this->hasPdoExtension()
        ];
    }

    /**
     * @return array{label: string, description: string, result: bool}
     */
    public function checkCacheDirIsWritable(): array
    {
        $cacheDir = $this->parameters->get('kernel.cache_dir');

        return [
            'label' => 'Cache folder write permission',
            'description' => "$cacheDir folder must be writable",
            'result' => is_writable($cacheDir)
        ];
    }

    /**
     * @return array{label: string, description: string, result: bool|int}
     */
    public function checkPhpVersion(): array
    {
        $requiredPhpVersion = $this->parameters->get('app.min_php_version');
        return [
            'label' => 'PHP Version',
            'description' => "PHP version must be at least $requiredPhpVersion (current version = " . PHP_VERSION . ')',
            'result' => version_compare(PHP_VERSION, $requiredPhpVersion, '>=')
        ];
    }

    /**
     * @return array{label: string, description: string, result: false|string}
     */
    public function checkTimezone(): array
    {
        $timezone = ini_get('date.timezone');

        return [
            'label' => 'PHP timezone',
            'description' => "Timezone must be configured in php.ini (current timezone = $timezone)",
            'result' => !empty($timezone)
        ];
    }

    /**
     * @return bool
     */
    public function hasGettextExtension(): bool
    {
        return in_array('gettext', $this->extensions);
    }

    /**
     * @return bool
     */
    public function hasSessionExtension(): bool
    {
        return in_array('session', $this->extensions);
    }

    /**
     * @return bool
     */
    public function hasPdoExtension(): bool
    {
        return in_array('PDO', $this->extensions);
    }
}
