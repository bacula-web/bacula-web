<?php
/*
+-------------------------------------------------------------------------+
| Copyright 2010-2011, Davide Franco                                              |
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
  session_start();
  include_once( 'config.inc.php' );

  $dbSql = new Bweb();

  // Enter your code here ...

  // Process and display the template
  $dbSql->tpl->display('client-report.tpl');
?>
