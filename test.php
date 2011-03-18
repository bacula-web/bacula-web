<?php
	require_once ("bweb.inc.php");
	$bw = new Bweb();
	
	// Check result icon
    $check_result = array( true => 's_ok.png', false => 's_error.gif' );
	
	// Checks list
	$check_list = array( array( 'check_cmd'   		=> 'php-gettext', 
								'check_label' 		=> 'PHP - Gettext support', 
								'check_descr' 		=> 'If you want Bacula-web in your language, please compile PHP with Gettext support' ),
						 array( 'check_cmd'   		=> 'pear-db', 
								'check_label' 		=> 'PEAR DB module', 
								'check_descr' 		=> 'PEAR DB support not found, please read the Bacula-web installation document'),
						 array( 'check_cmd'   		=> 'php-gd',
								'check_label' 		=> 'PHP - GD support',
								'check_descr' 		=> 'This is required by phplot, please compile php with GD support'),
						 array( 'check_cmd'   		=> 'php-mysql',
								'check_label' 		=> 'PHP - MySQL support',
								'check_descr'		=> 'PHP MySQL support must be installed in order to run bacula-web with MySQL bacula catalog'),
						 array( 'check_cmd'   		=> 'php-postgres',
								'check_label' 		=> 'PHP - PostgreSQL support',
								'check_descr'		=> 'PHP PostgreSQL support must be installed in order to run bacula-web with PostgreSQL bacula catalog'),								
						 array( 'check_cmd'   		=> 'smarty-cache',
								'check_label' 		=> 'Smarty cache folder write permission',
								'check_descr'		=> 'Smarty template engine need write permissions to templates_c folder')
				  );
	
	// Doing all checks
	foreach( $check_list as &$check ) {
		switch( $check['check_cmd'] )
		{
			case 'php-gettext':
				$check['check_result'] = $check_result[ function_exists( 'gettext' ) ];					
			break;
			case 'php-gd':
				$check['check_result'] = $check_result[ function_exists( 'gd_info') ];
			break;
			case 'pear-db':
				$check['check_result'] = $check_result[ class_exists('DB') ];
			break;
			case 'php-mysql':
				$check['check_result'] = $check_result[ function_exists('mysql_connect') ];
			break;
			case 'php-postgres':
				$check['check_result'] = $check_result[ function_exists('pg_connect') ];
			break;
			case 'smarty-cache':
				$check['check_result'] = $check_result[ is_writable( "./templates_c" ) ];
			break;
		}
		
	}

	// Generate test graph
 	$data = array( array('test', 100, 100, 200, 100), array('test1', 150, 100, 150, 100 ) );	
	$graph = new BGraph( "graph3.png" );
	$graph->SetColors( array('green', 'red' ) );

	$graph->SetData( $data, 'pie', 'text-data-single' );
	$graph->SetGraphSize( 400, 230 );
	
	$graph->Render();

	// Parse to template
	$bw->tpl->assign( 'checks', $check_list );
	$bw->tpl->assign('graph_test', $graph->Get_Image_file() );
	$bw->tpl->display('test.tpl');
?>
