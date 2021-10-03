<?php

/*
  +-------------------------------------------------------------------------+
  | Copyright 2010-2021, Davide Franco                                      |
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

class CErrorHandler
{
    private static $header;

    public static function displayError($exception)
    {
        switch (get_class($exception)) {
            case 'PDOException':
                self::$header = 'Database error';
                break;
            case 'Exception':
            default:
                self::$header = 'Application error';
                break;
        } // end switch

        $output = '';

        // Display PHP exception details
        $output .= '<br />';
        $output .= '<div class="panel panel-default">';
        $output .= '<div class="panel-heading">';
        $output .= '<h3 class="panel-title">';
        $output .= '<i class="fa fa-exclamation-triangle fa-lg"></i> ';
        $output .= self::$header . '</h3> </div>';
        $output .= '<div class="panel-body">';
        $output .= '<h4>Details</h4>';
        $output .= '<p>A problem with the description below happen</p>';
        $output .= '<b>Problem: </b>' . $exception->getMessage() . '<br />';
        $output .= '<h4>Help</h4>';
        $output .= "Have you tried to run the <a href='index.php?page=test'>test page</a> ?<br />";
        $output .= "Check the online documentation on <a href='http://www.bacula-web.org' target='_blank'>Bacula-Web project site</a> <br />";
        $output .= "Rebort a bug or suggest a new feature in the <a href='http://bugs.bacula-web.org' target='_blank'>Bacula-Web's bugtracking tool</a> <br />";
        $output .= '<h4>Debug</h4>';
        $output .= '<b>File: </b>' . $exception->getFile() . '<br />';
        $output .= '<b>Line: </b>' . $exception->getLine() . '<br />';
        $output .= '<b>Code: </b>' . $exception->getCode() . '<br />';
        $output .= '<h5>Exception trace</h5>';
        $output .= self::getFormatedTrace($exception);
        $output .= '</div> </div>';

        // Render Exception page
        $output = HtmlHelper::getHtmlHeader() . HtmlHelper::getNavBar() . '<div class="container">' . $output . '</div>' . HtmlHelper::getHtmlFooter();
        echo $output;
    } // end function displayError

    public static function getFormatedTrace($e)
    {
        $formated_trace  = '<table class="table">';
        
        foreach ($e->getTrace() as $exception) {
            $formated_trace .= '<tr>';
            $formated_trace .= '<td>';
            $formated_trace .= 'File: <b>' . $exception['file'] . '</b> ';
            $formated_trace .= 'on line <b>' . $exception['line'] . '</b> ';
            $formated_trace .= 'in function <b>' . $exception['class'] . $exception['type'] . $exception['function'] . '</b>';
            $formated_trace .= '</td>';
            $formated_trace .= '</tr>';
        }

        $formated_trace .= '</table>';
        
        return $formated_trace;
    }
}
