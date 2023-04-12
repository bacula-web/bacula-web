<?php

declare(strict_types=1);

/**
 * Copyright (C) 2010-2023 Davide Franco
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

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/core/bootstrap.php';

use Core\Db\DatabaseFactory;
use App\Tables\UserTable;
use Core\App\View;

/*
 * Function:    printUsage
 * Parameters:  none
 *
 */

function printUsage()
{
    echo "Bacula-Web version 8.9.0\n\n";
    echo "Usage:\n\n";
    echo "   php bwc [command]\n\n";
    echo "Available commands:\n\n";
    echo "   help\t\t\tPrint this help summary\n";
    echo "   check\t\tCheck requirements and permissions\n";
    echo "   setupauth\t\tSetup Apache authentication\n\n";
}

/*
 * Function:    getPassword
 * Parameters:  $prompt
 *
 */

function getPassword($prompt)
{
    // Save current tty settings
    $ostty = `stty -g`;

    // Set tty in silent mode
    system('stty -echo -icanon min 1 time 0 2>/dev/null || ' . 'stty -echo cbreak');

    echo "$prompt :";
    // Drop newline at the end of the string
    $input = substr(fgets(STDIN), 0, -1);
    echo "\n";

    // Restore tty settings
    system("stty $ostty");

    return $input;
}

/*
 * Display provided string in different color
 * @param string $string
 * @param string $type
 */

/**
 * Return provided string in coloredk based on $type parameter
 * Available colors are
 * red : 31
 * green :  32
 * orange: 33
 *
 * @param string $string
 * @param string[] $type
 * @return string
 */
function hightlight(string $string, $type = 'error'): string
{
    $colors = ['error' => '31', 'ok' => '32', 'warning' => '33', 'information' => '34'];

    $color = $colors[$type];
    return " \033[$color" . 'm' . $string . "\033[0m";
}

// Make sure the script is run from the command line
if (php_sapi_name() !== 'cli') {
    exit('You are not allowed to run this script from a web browser, but only from the command line');
}

// Make sure at least one parameter has been provided
if ($argc < 2) {
    echo "\nError: you should provide at least one command\n\n";
    printUsage();
    exit();
}

// Get command from user input
switch ($argv[1]) {
    case 'help':
        printUsage();
        break;
    case 'check':
        echo '=====================' . PHP_EOL;
        echo 'Checking requirements' . PHP_EOL;
        echo '=====================' . PHP_EOL . PHP_EOL;

        // Check PHP version
        $phpversion = phpversion();
        if (version_compare(PHP_VERSION, '7.4', '>=')) {
            echo "\tPHP version" . hightlight('Ok', 'ok') . " (using $phpversion)" . PHP_EOL;
        } else {
            echo 'PHP version' . hightlight('Error', 'error') . ']' . PHP_EOL;
            echo 'You have to upgrade PHP to at least version 7.4 ' . $phpversion . PHP_EOL;
        }

        // Check PHP timezone
        $timezone = ini_get('date.timezone');
        if (!empty($timezone)) {
            echo "\tPHP timezone" . hightlight('Ok', 'ok') . PHP_EOL;
        } else {
            echo "\tPHP timezone" . hightlight('Warning', 'warning') . PHP_EOL;
        }

        // Check assets folder permissions
        if (is_writable('application/assets/protected')) {
            echo "\tProtected assets folder is writable" . hightlight('Ok', 'ok') . PHP_EOL;
        } else {
            echo "\tProtected assets folder is writable" . hightlight('Error', 'error') . PHP_EOL;
        }

        // Check Smarty cache folder permissions
        $view = new View();
        if (is_writable($view->getCacheDir())) {
            echo "\tSmarty cache folder write permission" . hightlight('Ok', 'ok') . PHP_EOL;
        } else {
            echo "\tSmarty cache folder write permission" . hightlight('Error', 'error') . PHP_EOL;
        }

        // List available PHP PDO drivers
        echo PHP_EOL;
        echo 'Checking installed PHP database extensions' . PHP_EOL;
        echo '==========================================' . PHP_EOL . PHP_EOL;
        foreach ($pdo_drivers = PDO::getAvailableDrivers() as $driver) {
            echo "\t$driver" . hightlight('installed', 'ok') . PHP_EOL;
        }

        echo PHP_EOL;
        echo 'Checking installed PHP extensions' . PHP_EOL;
        echo '=================================' . PHP_EOL . PHP_EOL;

        // Check PHP SQLite support
        if (in_array('sqlite', PDO::getAvailableDrivers())) {
            echo "\tSQLite support" . hightlight('Ok', 'ok') . PHP_EOL;
        } else {
            echo "\tSQLite support" . hightlight('Error', 'error') . PHP_EOL;
        }

        // Check PHP Gettext support
        if (function_exists('gettext')) {
            echo "\tGettext support" . hightlight('Ok', 'ok') . PHP_EOL;
        } else {
            echo "\tGettext support" . hightlight('Error', 'error') . PHP_EOL;
        }

        // Check PHP Session support
        if (function_exists('session_start')) {
            echo "\tSession support" . hightlight('Ok', 'ok') . PHP_EOL;
        } else {
            echo "\tSession support" . hightlight('Error', 'error') . PHP_EOL;
        }

        // Check PHP PDO support
        if (class_exists('PDO')) {
            echo "\tPHP PDO support" . hightlight('Ok', 'ok') . PHP_EOL;
        } else {
            echo "\tPHP PDO support" . hightlight('Error', 'error') . PHP_EOL;
        }

        // Check PHP Posix support
        if (function_exists('posix_getpwuid')) {
            echo "\tPHP Posix support" . hightlight('Ok', 'ok') . PHP_EOL;
        } else {
            echo "\tPHP Posix support" . hightlight('Error', 'error') . PHP_EOL;
        }
        echo PHP_EOL;
        break;
    case 'setupauth':
        echo "It's now time to setup the application back-end database" . PHP_EOL;
        echo PHP_EOL . 'Please note that all informations stored in the user database will be destroyed' . PHP_EOL;
        echo 'Can we proceed ? ' . PHP_EOL;
        echo "\nAnswer (Yes/No): ";
        $answer = substr(fgets(STDIN), 0, -1);

        switch (strtolower($answer)) {
            case 'yes':
                echo "Let's go !" . PHP_EOL;
                break;
            case 'no':
                exit('Setup aborted' . PHP_EOL);
            break;
            default:
                exit('Wrong answer, aborting' . PHP_EOL);
        }

        echo 'Deleting application back-end database' . PHP_EOL;

        if (file_exists('application/assets/protected/application.db')) {
            if (unlink('application/assets/protected/application.db')) {
                echo "\tDatabase file removed" . hightlight('Ok', 'ok') . PHP_EOL;
            } else {
                die("\tFail to remove database file" . hightlight('Error', 'error') . PHP_EOL);
            }
        }

        // Create SQLite database
        try {
            // Create database schema
            echo 'Creating database schema' . PHP_EOL;

            $userTable = new UserTable(
                DatabaseFactory::getDatabase()
            );

            if ($userTable->createSchema() === 0) {
                echo "\tDatabase created" . hightlight('Ok', 'ok') . PHP_EOL;
            } else {
                echo "\tDatabase schema not created" . hightlight('Error', 'error') . PHP_EOL;
            }

            echo 'User creation' . PHP_EOL;

            echo 'Username: ';
            $username = substr(fgets(STDIN), 0, -1);

            echo 'Email address: ';
            $email = substr(fgets(STDIN), 0, -1);

            $password = getPassword('Password');

            if (strlen($password) < 6) {
                die("\tPassword must be at least 6 characters long, aborting" . hightlight('Error', 'error') . PHP_EOL);
            }

            $result = $userTable->addUser($username, $email, $password);
            if ($result === false) {
                echo '\t' . $result->rowCount() . 'ser created successfully' . hightlight('Ok', 'ok') . PHP_EOL;
            }

            echo PHP_EOL . 'You can now connect to your Bacula-Web instance using provided credentials' . PHP_EOL;
        } catch (PDOException $e) {
            die('Database error ' . $e->getMessage() . ' code(' . $e->getCode() . ')');
        }
        break;
    case 'publishAssets':
        echo 'Publishing assets' . PHP_EOL;

        $assets = [
            'css' => [
                /**
                 * Bootstrap CSS
                 */
                'vendor/twbs/bootstrap/dist/css/bootstrap.min.css',
                'vendor/twbs/bootstrap/dist/css/bootstrap.css.map',
                'vendor/twbs/bootstrap/dist/css/bootstrap.min.css.map',

                /**
                 * Bootstrap theme CSS
                 */
                'vendor/twbs/bootstrap/dist/css/bootstrap-theme.min.css',
                'vendor/twbs/bootstrap/dist/css/bootstrap-theme.css.map',
                'vendor/twbs/bootstrap/dist/css/bootstrap-theme.min.css.map',

                /**
                 * Bootstrap datetimepicker
                 */
                'vendor/components/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css',

                /**
                 * Bacula-Web custom css
                 */
                'application/assets/css/default.css',

                /**
                 * Font Awesome CSS
                 */
                'vendor/components/font-awesome/css/fontawesome.min.css',
                'vendor/components/font-awesome/css/all.css',

                /**
                 * Novus D3 CSS
                 */
                'vendor/novus/nvd3/build/nv.d3.css'
            ],
            'js' => [
                'vendor/novus/nvd3/build/nv.d3.js',
                'vendor/novus/nvd3/build/nv.d3.js.map',
                'vendor/mbostock/d3/d3.min.js',
                'vendor/components/jquery/jquery.min.js',
                'vendor/moment/moment/min/moment-with-locales.js',
                'vendor/twbs/bootstrap/dist/js/bootstrap.min.js',
                'vendor/components/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js',
                'application/assets/js/default.js',
                'application/assets/js/ie10-viewport-bug-workaround.js',
            ],
            'images' => [
                'application/assets/images/bacula-web-logo.png'
            ],
            'fonts' => [
                /**
                 * Bootstrap Gliyph icons
                 */
                'vendor/twbs/bootstrap/fonts/glyphicons-halflings-regular.woff2',
                'vendor/twbs/bootstrap/fonts/glyphicons-halflings-regular.woff',
                'vendor/twbs/bootstrap/fonts/glyphicons-halflings-regular.ttf',
            ],
            'webfonts' => [
                /**
                 * Font Awesome web fonts
                 */
                'vendor/components/font-awesome/webfonts/fa-brands-400.ttf',
                'vendor/components/font-awesome/webfonts/fa-brands-400.woff2',
                'vendor/components/font-awesome/webfonts/fa-regular-400.ttf',
                'vendor/components/font-awesome/webfonts/fa-regular-400.woff2',
                'vendor/components/font-awesome/webfonts/fa-solid-900.ttf',
                'vendor/components/font-awesome/webfonts/fa-solid-900.woff2',
                'vendor/components/font-awesome/webfonts/fa-v4compatibility.ttf',
                'vendor/components/font-awesome/webfonts/fa-v4compatibility.woff2',

            ]
        ];

        // Copy css assets
        foreach ($assets['css'] as $css) {
            $filename = basename($css);
            copy($css, "public/css/$filename");
        }

        // Copy javascript assets
        foreach ($assets['js'] as $js) {
            $filename = basename($js);
            copy($js, "public/js/$filename");
        }

        // Copy images assets
        foreach ($assets['images'] as $image) {
            $filename = basename($image);
            copy($image, "public/img/$filename");
        }

        // Copy fonts assets
        foreach ($assets['fonts'] as $font) {
            $filename = basename($font);
            copy($font, "public/fonts/$filename");
        }

        // Copy web fonts assets
        foreach ($assets['webfonts'] as $font) {
            $filename = basename($font);
            copy($font, "public/webfonts/$filename");
        }

        break;
    default:
        exit("\nError: unknown command, use <php bwc help> for further informations\n\n");
}
