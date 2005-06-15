<?

function Check($function,$text,$description="") {
	
	if  (!function_exists($function) )
		echo "<font color=red>NO</font></td> <td> ".$text." disabled</td><td>&nbsp;".$description;
	else
		echo "<font color=green>YES</font></td>  <td> ".$text." enabled<td>&nbsp;";
}

?>

<html>
<head>
	<title>Testing page</title>
</head>
<body>

Checking system for dependencies...<br><br><br>
<table width=100% border=0>
<tr>
	<td width=20%>
		Checking gettext:
		<? Check("gettext","Language support", "If you want view Bacula-web in your language, please compile PHP with Gettext");?>
	</td>
</tr>
<tr>
	<td width=20%>
		Checking Pear(DB):
		<? 
			if (@include_once("DB.php") )
				echo "<font color=green>YES</font></td>  <td> Pear DB enabled</td><td>&nbsp;";
			else
				echo "<font color=red>NO</font></td> <td> Pear DB NOT FOUND</td><td>This is required. Please download from <a href=\"http://pear.php.net/package/DB/download\">http://pear.php.net/package/DB/download</a> .";
		?>
	</td>
</tr>
<tr>
	<td>
		Checking GD:
		<?Check("gd_info","GD support", "This is required. Please, compile php with GD support");?>
	</td>
</tr>
</table>
<br><br>

Please, click the link below to test your graph system capabilities (Bacula-web only use PNG): <br>

<a href="external_packages/phplot/examples/test_setup.php" target="_blank">Test</a>
</body>
</html>