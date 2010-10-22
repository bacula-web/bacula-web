<?php
	require_once "DB.php";
	
	function Checkold($function,$text,$description="") 
	{
		if  (!function_exists($function) )
			echo "<td> ".$text." disabled</td><td>&nbsp;" . $description;
		else
			echo "<td> " . $text . " enabled<td>&nbsp;";
	}
	
	function Check( $support, $description, $error_message )
	{
		$result  = "Checking " . $description . " </td>";
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
			$result .= "<td width='300'>&nbsp;</td> <td> <img width='30' src='images/s_ok.gif' />";
		else
			$result .= "<td width='300'>$error_message</td> <td width='35'> <img width='30' src='images/s_error.gif' />";
		
		echo $result;
	}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" 
  "http://www.w3.org/TR/html4/loose.dtd">
<html lang="en">
<head>
	<title>Bacula-web - Test page</title>
	
	<style type="text/css">
		body{
			font-family: arial, verdana, helvetica;
			font-size: 10pt;
			padding-left: 100px;
		}
		table{
			
			margin: 20px 0px 0px 100px;
			border: thin solid black;
			border-collapse: collapse;
		}
		td{
			margin: 0px;
			padding: 1em;
			border-top: thin solid black;
		}
		img{
			margin-top: 1em;
		}
		
	</style>
</head>
<body>

<table>
<tr>
	<td width="300">
		<?php 
			Check( "php-gettext", "PHP Gettext support", "If you want Bacula-web in your language, please compile PHP with Gettext support" );
		?>
	</td>
</tr>
<tr>
	<td>
		<?php 
			Check("pear-db", "PEAR DB support", "PEAR DB support not found, please read the Bacula-web installation document");
		?>
	</td>
</tr>
<tr>
	<td>
		<?php
			Check( "php-gd", "PHP GD support", "This is required by phplot, please compile php with GD support" );
		?>
	</td>
</tr>
<tr>
	<td>
		<?php
			Check( "smarty-cache", "Smarty cache folder write permission", "Smarty template engine need write permissions to templates_c folder" );
		?>
	</td>
</tr>

<tr>
	<td colspan="3">
		<center>
			Testing your graph system capabilities (Bacula-web only use PNG) <br />
			<img src="simplegraph.php" /> 
		</center>
	</td>
</tr>
</table>

</body>

</html>