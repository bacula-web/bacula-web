<HTML>
<HEAD>
{popup_init src="js/overlib.js"}
{literal}
<script language="Javascript">
	function OpenWin(URL,wid,hei) {
		window.open(URL,"window1","width="+wid+",height="+hei+",scrollbars=yes,menubar=no,location=no,resizable=no")
	}
</script>
{/literal}
<TITLE>{#title#}</TITLE>
</HEAD>
<BODY bgcolor="#ffffff">
<table width=1000px cellpadding=0 cellspacing=0 border=0 bgcolor="#2F92AF">
 <tr>
 	<td class=titulo2 background="images/bg2.png" valign="bottom">
 	 {#title#}
 	</td>
	{if $dbs ne ""}
		<form method=post action=index.php>
		<td background="{#root#}/images/bg2.png" align="right" valign="top">
		{t}Select{/t}: 
		<select name=sel_database style="font-family:verdana;font-size: 10px;color: white; background-color:#666;" onchange=submit()>
		{html_options values=$dbs output=$dbs selected=$dbs_now}
		</select>
		</td>
		</form>
	{/if}
 	<td background="{#root#}/images/bg2.png" align=right width=7%>
	<a href="{php $_SERVER['PHP_SELF'];}"><img src="{#root#}/images/refresh.gif" alt='Refresh'></a>
 	<a href="http://indpnday.com/bacula_stuff/bacula-web/mantisbt/login_page.php" target="_blank" {popup text="They grow thanks to Juan Luis Francés...Please, click here to report them"}>
 	Bugs?
 	</a>
 	</td>
 	<td background="images/end2.png>
 	<img src="images/empty.png">
 	</td>
 </tr>
</table>