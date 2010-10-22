{config_load file=bacula.conf}
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
  {if !#IndexReport#}
    {include file=last_run_report.tpl} 	
  {else}
    {include file=report_select.tpl}
  {/if}
<!--  <table class=genmed cellspacing="1" cellpadding="3" border=0 align="center">

		<tr>
		  <td>
-->
	<div class="box">
		<p class="title">General report</p>
		{if $server==""} 
		  <img src="stats.php?server={$server}&tipo_dato=69&title=General%20report&modo_graph=bars&sizex=420&sizey=250&MBottom=20&legend=1" />
		{else}
		  <img src="stats.php?server={$server}&tipo_dato=3&title={$server}&modo_graph=bars" />
		{/if}
	</div> <!-- end div box -->
<!--
			</td>
		</tr>
  </table>
-->
</div>

{include file="footer.tpl"}