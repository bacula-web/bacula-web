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

/**
 * Setup app paths
 */

use Dotenv\Dotenv;

define('BW_ROOT', dirname(__DIR__));

require_once BW_ROOT . '/core/const.inc.php';

/**
 * Load app name and version from application/config/app using phpdotenv
 */
$dotenv = Dotenv::createImmutable(CONFIG_DIR, 'app');
$dotenv->load();
