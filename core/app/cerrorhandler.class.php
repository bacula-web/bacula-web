<?php

/**
 * Copyright (C) 2010-2022 Davide Franco
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

        $output .= '<div class="row"> <div class="col-xs-9">';

        // Error page header
        $output .= '<div class="page-header">
              <h3> <i class="fa fa-exclamation-triangle fa-lg"></i> '.self::$header.'<small> Oops, looks like something went wrong :(</small></h3>
              </div>';

        // Display PHP exception details

        $output .= '<h4>Details</h4>';
        $output .= '<p>A problem with the description below happen</p>';
        $output .= '<b>Problem: </b>' . $exception->getMessage() . '<br />';

        $output .= '<h4>Debug</h4>';
        $output .= '<b>File: </b>' . $exception->getFile() . '<br />';
        $output .= '<b>Line: </b>' . $exception->getLine() . '<br />';
        $output .= '<b>Code: </b>' . $exception->getCode() . '<br />';
        $output .= '<h5>Exception trace</h5>';
        $output .= self::getFormatedTrace($exception);

        $output .= "<hr /> <div class='well'> <h4>Found a bug, or need a new feature ?</h4> Feel free to submit a <a href='https://github.com/bacula-web/bacula-web/issues/new/choose' target='_blank' class='btn btn-default btn-warning btn-sm active'>bug report</a> or a <a href='https://github.com/bacula-web/bacula-web/issues' target='_blank' class='btn btn-default btn-primary btn-sm active'>feature request</a> </div>";

        $output .= '</div> ';

        // Right pane
        $output .= '<div class="col-xs-3">';
        $output .= '<div class="page-header"><h3>Need help ?</h3></div>';
        $output .= '<ul class="list-group">
                    <li class="list-group-item">
                    Using the <b>test page</b> could be helpful <br /><br />
                    <a class="btn btn-default btn-sm btn-info" href="index.php?page=test" target="_blank" rel="noopener noreferrer" role="button"><span class="glyphicon glyphicon-cog" aria-hidden="true"></span> Test page</a>
                    </li>';
        $output .= '<li class="list-group-item">
                    Official documentation <br /><br />
                    <a href="https://docs.bacula-web.org" target="_blank" rel="noopener noreferrer" class="btn btn-primary btn-default btn-sm active" role="button">Bacula-Web documentation</a>
                    </li>';
        $output .= '</ul>';
        $output .= '</div>';

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
