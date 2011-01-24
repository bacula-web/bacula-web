<?php
  require_once ("paths.php");
  require_once ($smarty_path."Smarty.class.php");
  require_once ("bweb.inc.php");

  $smarty = new Smarty(); 
  
  //require_once ("lang.php");

  // Smarty configuration
  $smarty->compile_check = true;
  $smarty->debugging = false;
  $smarty->force_compile = true;

  $smarty->template_dir = "./templates";
  $smarty->compile_dir = "./templates_c";
  $smarty->config_dir     = "./configs";  
  
	function Check( $support, $description, $error_message )
	{
		$result  = $description . " </td>";
		$ok 	 = false;
		
		switch( $support )
		{
			case 'php-gettext':
				if( function_exists( 'gettext') )
					$ok = true;
			break;
			case 'php-gd':
				if( function_exists( 'gd_info') )
					$ok = true;
			break;
			case 'pear-db':
				if ( class_exists('DB') )
					$ok = true;
			break;
			case 'smarty-cache':
				if ( is_writable( "./templates_c" ) )
					$ok = true;
			break;
		} 
		if( $ok )
			$result .= "<td width='300'>&nbsp;</td> <td> <img width='25' src='style/images/s_ok.png' />";
		else
			$result .= "<td width='300'>$error_message</td> <td width='35'> <img width='30' src='style/images/s_error.gif' />";
		
		echo $result;
	}
	$smarty->display('test.tpl');
?>
