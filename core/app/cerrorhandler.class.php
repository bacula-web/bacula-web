<?php

/*
  +-------------------------------------------------------------------------+
  | Copyright 2010-2012, Davide Franco                                              |
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

class CErrorHandler {

    private static $header;
    private static $debug;

    // Define Header
    public function setHeader($header) {
        self::$header = $header;
    }

    // Enable debug mode
    public function setDebug($debug = true) {
        self::$debug = $debug;
    }

    public static function displayError($exception) {
        switch (get_class($exception)) {
            case 'PDOException':
                self::setHeader('Database error');
                break;
            case 'Exception':
            default:
                self::setHeader('Application error');
                break;
        } // end switch

        echo '<h4 style="padding: 0px 5px; font-family:Arial,Verdana;">' . self::$header . '</h4>';
        echo '<p style="width: 1000px; padding: 0px 5px; font-family: Arial,Verdana; font-size: 10pt;">';
        echo '<b>Message:</b> ' . $exception->getMessage() . '<br />';

        // Show more information is debug mode is enabled
        if (self::$debug) {
            echo 'Code: ' . $exception->getCode() . '<br />';
            echo 'Line: ' . $exception->getLine() . '<br />';
            echo 'File: ' . $exception->getFile() . '</p>';
        }

        // Display footer
        $footer = '<p style="font-size: 10pt; background-color: #F0F0F0; width: 1000px; padding: 10px; font-family: Arial,Verdana;">';
        $footer .= 'Have you try to run the <a href="test.php">test page</a> ?<br />';
        $footer .= 'Check the online documentation on <a href="http://www.bacula-web.org" target="_blank">Bacula-Web project site</a> <br />';
        $footer .= 'Rebort a bug or suggest a new feature in the <a href="http://www.bacula-web.org/bugs" target="_blank">Bacula-Web\'s bugtracking tool</a> <br /> </p>';
        echo $footer;

        die();
    }

}
?>

