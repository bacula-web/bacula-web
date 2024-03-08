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

// Configuration
const CONFIG_DIR = BW_ROOT . 'application/config/';
const CONFIG_FILE = CONFIG_DIR . 'config.php';

const TPL_DIR = BW_ROOT . 'application/views/templates';
const TPL_CACHE = BW_ROOT . 'application/views/cache';

// Locales
const LOCALE_DIR = BW_ROOT . 'application/locale';

// Time intervals in secondes
define('FIRST_DAY', mktime(0, 0, 0, 1, 1, 1970));
define('DAY', 86400);
define('WEEK', 7 * DAY);
define('MONTH', 4 * WEEK);

// Job status code
define('J_NOT_RUNNING', 'C');
define('J_RUNNING', 'R');
define('J_BLOCKED', 'B');
define('J_COMPLETED', 'T');
define('J_COMPLETED_ERROR', 'E');
define('J_NO_FATAL_ERROR', 'e');
define('J_FATAL', 'f');
define('J_CANCELED', 'A');
define('J_WAITING_CLIENT', 'F');
define('J_WAITING_SD', 'S');
define('J_WAITING_NEW_MEDIA', 'm');
define('J_WAITING_MOUNT_MEDIA', 'M');
define('J_WAITING_STORAGE_RES', 's');
define('J_WAITING_JOB_RES', 'j');
define('J_WAITING_CLIENT_RES', 'c');
define('J_WAITING_MAX_JOBS', 'd');
define('J_WAITING_START_TIME', 't');
define('J_WAITING_HIGH_PR_JOB', 'p');
define('J_VERIFY_FOUND_DIFFERENCES', 'D');

// Job levels
define('J_FULL', 'F');
define('J_DIFF', 'D');
define('J_INCR', 'I');
