#!/usr/bin/env php
<?php
/*
  +-------------------------------------------------------------------------+
  | Copyright (C) 2004 Juan Luis Frances Jimenez						    |
  | Copyright 2010-2018, Davide Franco                                      |
  |                                                                         |
  | This program is free software; you can redistribute it and/or           |
  | modify it under the terms of the GNU General Public License             |
  | as published by the Free Software Foundation; either version 2          |
  | of the License, or (at your option) any later version.                  |
  |                                                                         |
  | This program is distributed in the hope that it will be useful,         |
  | but WITHOUT ANY WARRANTY; without even the implied warranty of          |
  | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the           |
  | GNU General Public License for more details.                            |
  +-------------------------------------------------------------------------+
 */

require_once( 'core/global.inc.php' );

/*
 * Function:    printUsage
 * Parameters:  none
 *
 */

function printUsage() {
    echo "Bacula-Web version 8.0.0-RC1\n\n";
    echo "Usage:\n";
    echo "   php console [command]\n\n";
    echo "Available commands:\n";
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
    system("stty -echo -icanon min 1 time 0 2>/dev/null || " . "stty -echo cbreak");

    echo "$prompt :";
    // Drop newline at the end of the string
    $input = substr(fgets(STDIN), 0, -1);
    echo "\n";

    // Restore tty settings
    system ("stty $ostty");

    return $input;
}

/*
 * Display provided string in different color
 * @param string $string
 * @param string $type 
 */

function hightlight( $string, $type = 'error' ) {

/* shell colors
 * red : 31
 * green :  32
 * orange: 33
 */
    $colors = array( 'error' => '31', 'ok' => '32', 'warning' => '33', 'information' => '34');
    
    $color = $colors[$type];
    echo "\033[$color"."m" . $string . "\033[0m";
}

// Make sure the script is run from the command line
if(!(php_sapi_name() === 'cli')) {
    exit("You are not allowed to run this script from a web browser, but only from the command line");
} 

// Make sure at least one parameter has been provided
if( $argc < 2) {
    echo "\nError: you should provide at least one command\n\n";
    exit(printUsage());
}

// Get command from user input 
switch( $argv[1]) {
case'help':
    printUsage();
    break;
case 'check':
    echo PHP_EOL . '=======================' . PHP_EOL;
    echo 'Checking requirements' . PHP_EOL;
    echo '=======================' . PHP_EOL . PHP_EOL;

    // Check PHP version
    if(version_compare(PHP_VERSION, '5.6', '>=')) {
          echo "\tPHP version" . hightlight( 'Ok', 'ok') . PHP_EOL;
    }else{
          echo "PHP version" . hightlight( 'Error', 'error') . ']' . PHP_EOL;
    }

    // Check PHP timezone
    $timezone = ini_get('date.timezone');
    if (!empty($timezone)){
          echo "\tPHP timezone" . hightlight( 'Ok', 'ok') . PHP_EOL;
    }else{
          echo "\tPHP timezone" . hightlight( 'Warning', 'warning') . PHP_EOL;
    }

    // Check assets folder permissions
    if( is_writable('application/assets/protected')) {
          echo "\tProtected assets folder is writable" . hightlight( 'Ok', 'ok') . PHP_EOL;
    }else{
          echo "\tProtected assets folder is writable" . hightlight( 'Error', 'error') . PHP_EOL;
    }

    // Check Smarty cache folder permissions
    if(is_writable(VIEW_CACHE_DIR)) {
          echo "\tSmarty cache folder write permission" . hightlight( 'Ok', 'ok') . PHP_EOL;
    }else{
          echo "\tSmarty cache folder write permission" . hightlight( 'Error', 'error') . PHP_EOL;
    }

    // Check PHP Posix support
    if(function_exists('posix_getpwuid')) {
          echo "\tPHP Posix support" . hightlight( 'Ok', 'ok') . PHP_EOL;
    }else{
          echo "\tPHP Posix support" . hightlight( 'Error', 'error') . PHP_EOL;
    }

    // Check PHP PDO support
    if(class_exists('PDO')) {
          echo "\tPHP PDO support" . hightlight( 'Ok', 'ok') . PHP_EOL;
    }else{
          echo "\tPHP PDO support" . hightlight( 'Error', 'error') . PHP_EOL;
    }

    // Check PHP SQLite support
    if( in_array('sqlite', PDO::getAvailableDrivers() )) {
          echo "\tPHP SQLite support" . hightlight( 'Ok', 'ok') . PHP_EOL;
    }else{
          echo "\tPHP SQLite support" . hightlight( 'Error', 'error') . PHP_EOL;
    }

    // List available PHP PDO drivers
    echo PHP_EOL . hightlight('PDO drivers (available):', 'information');
    foreach ($pdo_drivers = PDO::getAvailableDrivers() as $driver) { echo "\t driver: $driver" . PHP_EOL; }

    // Check PHP Gettext support
    if(function_exists('gettext')) {
          echo "\tPHP Gettext support" . hightlight( 'Ok', 'ok') . PHP_EOL;
    }else{
          echo "\tPHP Gettext support" . hightlight( 'Error', 'error') . PHP_EOL;
    }

    // Check PHP Session support
    if(function_exists('session_start')) {
          echo "\tPHP Session support" . hightlight( 'Ok', 'ok') . PHP_EOL;
    }else{
          echo "\tPHP Session support" . hightlight( 'Error', 'error') . PHP_EOL;
    }

    break;
case 'setupauth':

    echo "It's now time to setup the application back-end database" . PHP_EOL;
    echo PHP_EOL . "Please note that all informations stored in the user database will be destroyed" . PHP_EOL;
    echo "Can we proceed ? " . PHP_EOL;
    echo "\nAnswer (Yes/No): ";
    $answer = substr(fgets(STDIN), 0, -1);

    switch(strtolower($answer)) {
    case "yes":
        echo "Let's go !" . PHP_EOL;
        break;
    case "no":
        exit("Setup aborted" . PHP_EOL);
        break;
    default:
        exit("Wrong answer, aborting" . PHP_EOL);
    }

    echo "Deleting application back-end database" . PHP_EOL;

    if( file_exists('application/assets/protected/application.db')){
        if(unlink('application/assets/protected/application.db')) {
            echo "\tDatabase file removed" . hightlight( 'Ok', 'ok') . PHP_EOL;
        }else{
            die("\tFail to remove database file" . hightlight( 'Error', 'error') . PHP_EOL);
        }
    }

    // Create SQLite database
    try {
        $pdo = new PDO('sqlite:application/assets/protected/application.db');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        echo "\tDatabase created" . hightlight( 'Ok', 'ok') . PHP_EOL;

        // Create database schema
        echo "Creating database schema" . PHP_EOL;
    
        $createSchemaQuery = 'CREATE TABLE IF NOT EXISTS Users (
            user_id INTEGER PRIMARY KEY,
            username TEXT NOT NULL UNIQUE,
            passwordHash TEXT NOT NULL,
            email TEXT
        );
        CREATE INDEX IF NOT EXISTS User_ix_username ON Users (username);';

        $rows = $pdo->exec($createSchemaQuery);

        if($rows === FALSE) {
            echo "\tDatabase schema not created" . hightlight( 'Error', 'error') . PHP_EOL;
        }else{
            echo "\tDatabase schema created" . hightlight( 'Ok', 'ok') . PHP_EOL;
        }

        echo "User creation" . PHP_EOL;

        echo "Username: ";
        $username = substr(fgets(STDIN), 0, -1);

        echo "Email address: ";
        $email = substr(fgets(STDIN), 0, -1);

        $password = getPassword("Password");

        if(strlen($password) < 6){
              die("\tPassword must be at least 6 characters long, aborting" . hightlight( 'Error', 'error') . PHP_EOL);
        }

        $hashedPassword = password_hash( $password, CRYPT_BLOWFISH);
        $addUserQuery = "INSERT INTO Users (username,email,passwordHash) VALUES ('$username','$email', '$hashedPassword');";
        $createdUser = $pdo->exec($addUserQuery);

        if($createdUser > 0) {
                echo "\tUser created" . hightlight( 'Ok', 'ok') . PHP_EOL;
        }

        echo PHP_EOL . "You can now connect to your Bacula-Web instance using provided credentials" . PHP_EOL;

    }catch(PDOException $e){
          die( 'Database error ' . $e->getMessage() . ' code(' . $e->getCode() . ')');
    }
    break;
default:
    exit("\nError: unknown command, use <php console help> for further informations\n\n");
}

?>
