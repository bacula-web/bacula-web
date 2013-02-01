<?php

/*
  +-------------------------------------------------------------------------+
  | Copyright 2010-2013, Davide Franco                                              |
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

    // Define Header
    public function setHeader($header) {
        self::$header = $header;
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
		
		$output  = '<table style="margin: 10px; width: 900px; border: 1px solid #c0c0c0;">';
		
		// Header
		$output .= "<tr> <th colspan='2'>" . self::$header . "</th> </tr>";
		
		// Exception details
		$output .= "<tr> <td class='left_align' width='200'>File</td> <td class='left_align'>" . $exception->getFile() . "</td> \n</tr> \n";
		$output .= "<tr> <td class='left_align'>Line</td> <td class='left_align'>" . $exception->getLine() . "</td> \n </tr> \n";
		$output .= "<tr> <td class='left_align'>Exception code</td> <td class='left_align'>" . $exception->getCode() . "</td> \n </tr> \n";
		$output .= "<tr> <td class='left_align'>Exception message</td> <td class='left_align'>" . $exception->getMessage() . "</td> \n </tr> \n";
		
		$output .= "<tfoot> \n <tr> \n";
		$output .= "<td class='left_align' colspan='2'> \n";
		$output .= "Have you try to run the <a href='test.php'>test page</a> ?<br />";
		$output .= "Check the online documentation on <a href='http://www.bacula-web.org' target='_blank'>Bacula-Web project site</a> <br />";
		$output .= "Rebort a bug or suggest a new feature in the <a href='http://bugs.bacula-web.org' target='_blank'>Bacula-Web\'s bugtracking tool</a> <br />";
		$output .= "</td> \n";
		$output .= " \n</tr> \n </tfoot>";
		
		$output .= "</table>";
		
		echo $output;
        die();
    }

}
?>

