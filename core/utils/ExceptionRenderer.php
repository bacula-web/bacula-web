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

namespace Core\Utils;

use App\Libs\FileConfig;
use Core\Exception\PageNotFoundException;
use Error;
use Exception;
use PDOException;
use Throwable;

/**
 * Generate html content for Exception or Error
 */
class ExceptionRenderer
{
    private static $header = [
        PDOException::class => 'Database error',
        ConfigFileException::class => 'Configuration error',
        PageNotFoundException::class => 'Page not found',
        Error::class => 'PHP error',
        Exception::class => 'Application error'
    ];

    /**
     * @var Throwable
     */
    private static Throwable $throwable;

    /**
     * @param Error $error
     * @return string
     */
    public static function renderError(Error $error): string
    {
        self::$throwable = $error;

        $content = 'something bad happen ' .
            $error->getCode() . ' ' .
            $error->getMessage() . ' ' .
            $error->getFile() . ' ' .
            $error->getLine() . ' ' .
            get_class($error);

        return self::render($content);
    }

    /**
     * @param Exception $exception
     * @return string
     * @throws ConfigFileException
     */
    public static function renderException(Exception $exception): string
    {
        self::$throwable = $exception;

        FileConfig::open(CONFIG_FILE);
        $content = '';

        /**
         * Treat PageNotFoundException differently as we just want to
         * display a short message and a link to home page
         */

        if (get_class($exception) === PageNotFoundException::class) {
            $content = 'This page does not exist, please go back to <a href=\'index.php\'>home page</a>';
        } elseif (FileConfig::get_Value('debug')) {
            $content = self::getTrace($exception) .
                $exception->getCode() . ' ' .
                $exception->getMessage() . ' ' .
                $exception->getFile() . ' ' .
                $exception->getLine() . ' ' .
                get_class($exception);
        }

        return self::render($content);
    }

    /**
     * @param Exception|Error $e
     * @return string
     */
    private static function getTrace($e): string
    {
        $formated_trace = '<table class="table">';

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

    /**
     * @param string $content
     * @return string
     */
    private static function render(string $content): string
    {
        $errortype = self::$header[get_class(self::$throwable)] ?? 'Core error';

        return HtmlHelper::getHtmlHeader() .
            HtmlHelper::getNavBar() .
            '<div class="container">' .
            "<div class=\"col-xs-8\">" .
            self::getPageHeader() .
            "<h3>$errortype</h3>" .
            $content .
            '</div>' .
            '<div class=\'col-xs-4\'> ' . self::getHelpColumn() . '</div>' .
            '</div>' .
            HtmlHelper::getHtmlFooter();
    }

    private static function getHelpColumn(): string
    {
        return '<div class="page-header"><h3>Need further help ?</h3></div>' .
                    '<ul class="list-group">' .
                        '<li class="list-group-item">' .
                            '<h4>Health check</h4>' .
                                '<p>Use the <b>test page</b> to make sure your setup health is fine</p>' .
                                '<a class="btn btn-default btn-sm btn-info" href="index.php?page=test" target="_blank" rel="noopener noreferrer" role="button"><span class="glyphicon glyphicon-cog" aria-hidden="true"></span> Test page</a>' .
                        '</li>' .
                        '<li class="list-group-item">' .
                            '<h4>Official documentation</h4>' .
                                '<a href="https://docs.bacula-web.org" target="_blank" rel="noopener noreferrer" class="btn btn-primary btn-default btn-sm active" role="button">Bacula-Web documentation</a>' .

                        '</li>' .
                        '<li class="list-group-item">' .
                            '<h4>Found a bug ?</h4>' .
                        '<p>
                            If you think that you have found a bug, Feel free to submit a <a href=\'https://github.com/bacula-web/bacula-web/issues/new/choose\' target=\'_blank\' class=\'btn btn-default btn-warning btn-sm active\'>bug report</a>
                        </p>' .
                        '<h4>Missing feature ?</h4>' .
                        '<p>Feeling that a feature is missing ? Feel free to open a
                            <a href=\'https://github.com/bacula-web/bacula-web/issues\' target=\'_blank\' class=\'btn btn-default btn-primary btn-sm active\'>feature request</a></p>' .
                        '</li>' .
                    '</ul>' .
                '</div>';
    }

    private static function getPageHeader(): string
    {
        return '<div class="page-header">
              <h3> <i class="fa fa-exclamation-triangle fa-lg"></i><small> Oops, it looks like something went wrong somehow :(</small></h3>
              </div>';
    }
}
