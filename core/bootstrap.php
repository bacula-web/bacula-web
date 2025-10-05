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

use Dotenv\Dotenv;

define('BW_ROOT', dirname(__DIR__));

// Configuration
const CONFIG_DIR = BW_ROOT . '/application/config/';
const CONFIG_FILE = CONFIG_DIR . 'config.php';

const TPL_DIR = BW_ROOT . '/application/views/templates';
const TPL_CACHE = BW_ROOT . '/application/views/cache';

// Locales
const LOCALE_DIR = BW_ROOT . '/application/locale';

// Time intervals in secondes
define('FIRST_DAY', mktime(0, 0, 0, 1, 1, 1970));
define('DAY', 86400);
define('WEEK', 7 * DAY);
define('MONTH', 4 * WEEK);

// Job levels
define('J_FULL', 'F');
define('J_DIFF', 'D');
define('J_INCR', 'I');

$dotenv = Dotenv::createImmutable(CONFIG_DIR, 'app');
$dotenv->load();
