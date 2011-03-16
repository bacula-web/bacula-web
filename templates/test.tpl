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
	  <p class="title">Required components</p>
	<table border="0">
		{foreach from=$checks item=check}
		  <tr>
		    <td width="250">{$check.check_label}</td>
			<td width="450">{$check.check_descr}</td>
			<td  style="text-align:center;"> <img src='style/images/{$check.check_result}' width='20' alt=''/></td>
		  </tr>
		{/foreach}
	</table>
	</div> <!-- end div class=box -->
	
	<div class="box">
	  <p class="title">Graph</p>
	<table>
	  <tr>
	    <td>
			Graph system capabilities (Bacula-web only use PNG image format)
		</td>
	    <td colspan="2">
		  <img src="{$graph_test}" alt='' />
	    </td>
	  </tr>
	</table>
	</div> <!-- end div class=box -->
</div> <!-- end div id=main_center -->

</body>
</html>
