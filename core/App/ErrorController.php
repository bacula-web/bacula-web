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

namespace Core\App;

use App\Libs\FileConfig;
use Core\Exception\ConfigFileException
use Symfony\Component\HttpFoundation\Response;
use Core\Utils\HtmlHelper;
use Exception;
use Error;use Throwable;

class ErrorController
{
    /**
     * @var string
     */
    private static string $header = '';

    /**
     * @var int
     */
    private static int $statusCode = 500;

    /**
     * @var string
     */
    private static string $message = '';

    /**
     * @param Throwable $exception
     * @return Response
     * @throws ConfigFileException
     */
    public static function handle(Throwable $exception): Response
    {
        switch (get_class($exception)) {
            case 'Core\Exception\PageNotFoundException':
                self::$header = 'Page not found';
                self::$statusCode = 404;
                self::$message = 'This page does not exist, please go back to the <a href="index.php" class="btn btn-default btn-primary btn-sm active">Home page</a>';
                break;
            case 'PDOException':
                self::$header = 'Database error';
                break;
            case 'Core\Exception\ConfigFileException':
                self::$header = 'Configuration error';
                break;
            case 'Error':
            case 'TypeError':
                self::$header = 'PHP Error';
                break;
            case 'Exception':
            default:
                self::$header = 'Application error';
                break;
        }

        $output = '<div class="row"> <div class="col-xs-8">';

        // Error page header
        $output .= '<div class="page-header">
              <h3> <i class="fa fa-exclamation-triangle fa-lg"></i><small> Oops, it looks like something went wrong somehow :(</small></h3>
              </div>';

        $output .= '<h3>' . self::$header . '</h3>' . self::$message;

        // Display PHP exception details

        $output .= $exception->getMessage() . '<br />';

        FileConfig::open(CONFIG_FILE);
        try {
            if (FileConfig::get_Value('debug')) {
                $output .= '<h4>Debug</h4>';
                $output .= '<b>File: </b>' . $exception->getFile() . '<br />';
                $output .= '<b>Line: </b>' . $exception->getLine() . '<br />';
                $output .= '<b>Code: </b>' . $exception->getCode() . '<br />';
                $output .= '<h5>Exception trace</h5>';

                $errorsClasses = [
                    'TypeError',
                    'Error'
                ];

                if (array_key_exists(get_class($exception), $errorsClasses)) {
                    $output .= self::getFormattedTrace($exception);
                }
            }
        } catch( ConfigFileException $exception)
        {
            throw new ConfigFileException('Error: unable to load config file');
        }


        $output .= '</div> ';

        // Right pane
        $output .= '<div class="col-xs-4">';
        $output .= '<div class="page-header"><h3>Need further help ?</h3></div>';
        $output .= '<ul class="list-group">
                    <li class="list-group-item">
                        <h4>Health check</h4>
                        <p>Use the <b>test page</b> to make sure your setup health is fine</p>
                    <a class="btn btn-default btn-sm btn-info" href="index.php?page=test" target="_blank" rel="noopener noreferrer" role="button"><span class="glyphicon glyphicon-cog" aria-hidden="true"></span> Test page</a>
                    </li>';
        $output .= '<li class="list-group-item">
                    <h4>Official documentation</h4>
                    <a href="https://docs.bacula-web.org" target="_blank" rel="noopener noreferrer" class="btn btn-primary btn-default btn-sm active" role="button">Bacula-Web documentation</a>
                    </li>';
        $output .= '<li class="list-group-item">';
        $output .= '<h4>Found a bug ?</h4>
                    <p>
                    If you think that you have found a bug, Feel free to submit a <a href=\'https://github.com/bacula-web/bacula-web/issues/new/choose\' target=\'_blank\' class=\'btn btn-default btn-warning btn-sm active\'>bug report</a>
                    </p>
                    <h4>Missing feature ?</h4>';
        $output .= '<p>Feeling that a feature is missing ? Feel free to open a
                    <a href=\'https://github.com/bacula-web/bacula-web/issues\' target=\'_blank\' class=\'btn btn-default btn-primary btn-sm active\'>feature request</a></p>';
        $output .= '</li>';
        $output .= '</ul>';
        $output .= '</div>';

        // Render Exception page
        $output = HtmlHelper::getHtmlHeader() . HtmlHelper::getNavBar() . '<div class="container">' . $output . '</div>' . HtmlHelper::getHtmlFooter();

        $response = new Response($output);
        $response->setStatusCode(self::$statusCode);
        return $response;
    }

    /**
     * @param Exception|Error $e
     * @return string
     */
    private static function getFormattedTrace($e): string
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
