<?php
/* 
+-------------------------------------------------------------------------+
| Copyright 2010-2012, Davide Franco			                          |
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
  include_once( 'core/global.inc.php' );

  $view = new CView();
  $dbSql = new Bweb($view);
  
  $query 	= "";
  $joblogs 	= array();
  
  $get_vars = CHttpRequest::getRequestVars( $_GET );
  $jobid    = $get_vars['jobid'];
  
  $query 	= CDBQuery::getQuery( array('table' => 'Log', 'where' => "JobId = '$jobid'", 'orderby' => 'Time') );
  $result 	= $dbSql->db_link->runQuery( $query );
  
  foreach( $result->fetchAll() as $log )
  {
	// Odd or even row
	if( count($joblogs) % 2) {
	  $log['class'] = 'odd';
	}
	
	$joblogs[] = $log;
  }
  
  $view->assign( 'jobid', $jobid );
  $view->assign( 'joblogs', $joblogs );
  
  // Process and display the template 
  $view->render('joblogs.tpl');
?>
