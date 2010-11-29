<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" 
  "http://www.w3.org/TR/html4/loose.dtd">
<html lang="en">
<head>
{literal}
<script type="text/javascript">
	function OpenWin(URL,wid,hei) {
		window.open(URL,"window1","width="+wid+",height="+hei+",scrollbars=yes,menubar=no,location=no,resizable=no")
	}
</script>
{/literal}
<link rel="stylesheet" type="text/css" href="style/default.css" />
<title>bacula-web</title>
</head>
<body>
{popup_init src='./js/overlib.js'}
{include file=header.tpl}

<div id="main_left">
{include file=generaldata.tpl}
<br /> 
{include file=volumes.tpl}
</div>

<div id="main_right">
	{include file="$last_report"} 	
  
<!--  <table class=genmed cellspacing="1" cellpadding="3" border=0 align="center">
		<tr>
		  <td>
-->
	<div class="box">
		<p class="title">General report</p>
		{if $server==""} 
		  <img src="stats.php?server={$server}&amp;tipo_dato=69&amp;title=General%20report&amp;modo_graph=bars&amp;sizex=420&amp;sizey=250&amp;MBottom=20&amp;legend=1" alt="" />
		{else}
		  <img src="stats.php?server={$server}&amp;tipo_dato=3&amp;title={$server}&amp;modo_graph=bars" alt="" />
		{/if}
	</div> <!-- end div box -->
<!--
			</td>
		</tr>
  </table>
-->
</div>

{include file="footer.tpl"}