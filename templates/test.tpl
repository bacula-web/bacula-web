<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" 
  "http://www.w3.org/TR/html4/loose.dtd">
<html lang="en">
<head>
<title>bacula-web</title>
<link rel="stylesheet" type="text/css" href="style/default.css">
</head>
<body>
{include file=header.tpl}
  <div id="nav">
    <a href="index.php" title="Back to the dashboard">Dashboard</a> > Test page
  </div>
  <div id="main_center">
	<div class="box">
	  <p class="title">Testing required components</p>
	  
	  <table>
	    <tr>
	      <td width="300">
		    {php}Check( "php-gettext", "PHP Gettext support", "If you want Bacula-web in your language, please compile PHP with Gettext support" );{/php}
		  </td>
		</tr>
		<tr>
		  <td>
		    {php}Check("pear-db", "PEAR DB support", "PEAR DB support not found, please read the Bacula-web installation document");{/php}
		  </td>
	    </tr>
		<tr>
		  <td>
		  {php}Check( "php-gd", "PHP GD support", "This is required by phplot, please compile php with GD support" );{/php}
		  </td>
		</tr>
		<tr>
		  <td>
			{php}Check( "smarty-cache", "Smarty cache folder write permission", "Smarty template engine need write permissions to templates_c folder" );{/php}
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

	  
	</div>
  </div>
</body>
</html>