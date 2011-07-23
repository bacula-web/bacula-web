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
	include_once( 'config/global.inc.php' );

	$dbSql = new Bweb();
	$clientid 		= '';
    $client			= '';
	$client_jobs	= array();
	
	$http_post = CHttpRequest::getRequestVars( $_POST ); 
	$http_get  = CHttpRequest::getRequestVars( $_GET );
	
	if( isset( $http_post['client_id'] ) )
		$clientid = $http_post['client_id'];
	elseif( isset( $http_get['client_id'] ) )
		$clientid = $http_get['client_id'];
	else
		die( "Application error: Client not specified " );
		
	// Client informations
	$client	= $dbSql->getClientInfos($clientid);
	
	$dbSql->tpl->assign( 'client_name', $client['name']);
	$dbSql->tpl->assign( 'client_os', $client['os']);
	$dbSql->tpl->assign( 'client_arch', $client['arch']);
	$dbSql->tpl->assign( 'client_version', $client['version']);
	
	// Process and display the template
	$dbSql->tpl->display('client-report.tpl');
?>
