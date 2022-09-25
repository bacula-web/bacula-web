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

namespace Core\Utils;

class HtmlHelper
{

    /**
    * Return html header
    * @return string
    */

    public static function getHtmlHeader()
    {
        $htmlHeader = '<!DOCTYPE html>
                        <html lang="en">

                        <head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Bacula-Web - Application error</title>

  <!-- Bootstrap front-end framework -->
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <link rel="stylesheet" href="css/bootstrap-theme.min.css">

  <!-- Custom css -->
  <link rel="stylesheet" href="css/default.css">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="css/font-awesome.min.css">

  <meta http-equiv="Content-Type" content="text/html;charset=utf-8" >
</head>

<body>';

        return $htmlHeader;
    }

    /**
     * Return Bootstrap navbar
     * @return string
     */

    public static function getNavBar()
    {
        $navbar = '<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
                   <div class="container-fluid">
                   <div class="navbar-header">
                   <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
                   <span class="sr-only">Toggle navigation</span>
                   <span class="icon-bar"></span>
                   <span class="icon-bar"></span>
                   <span class="icon-bar"></span>
                   </button>
                   <a class="navbar-brand" href="index.php">Bacula-Web</a>
                   </div> <!-- div class="navbar-header" -->
        
                   </div> <!-- div class="collapse navbar-collapse"-->
                   </div> <!-- div class="container-fluid" -->
                   </div> <!-- class="navbar" -->
                   ';
        return $navbar;
    }

    /**
    * Return html footer
    * @return string
    */

    public static function getHtmlFooter()
    {
        $htmlFooter = '<!-- JQuery and Bootstrap Javascript -->
                    <script src="js/jquery/jquery.min.js"></script>
                    <script src="js/moment-with-locales.js"></script>
                    <script src="js/bootstrap.min.js"></script>

                    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
                    <script src="application/assets/js/ie10-viewport-bug-workaround.js"></script>
                    </body>
                    </html>';

        return $htmlFooter;
    }
}

// end class
