<!-- Header -->
<div id="header">
 <p>bacula-web</p>
</div> <!-- end div header -->

<div id="subheader">
 <ul>
	<li> 
		<a href="index.php">Refresh</a>
	</li>
	<li>
		<a href="http://bacula-web.dflc.ch/bugs" target="_blank">Bugs</a
	<li>
		<a href="http://bacula-web.dflc.ch" target="_blank">About</a>
	</li>
 </ul>
</div> <!-- end div subheader -->

<!-- Multi catalog selection will added in next release
<table width="100%" cellpadding=0 cellspacing=0 border=0 bgcolor="#2F92AF">
 <tr>

	{if $dbs ne ""}
		<form method=post action=index.php>
		<td background="{#root#}/images/bg2.png" align="right" valign="top">
		{t}Select{/t}: 
		<select name=sel_database style="font-family:verdana;font-size: 10px;color: white; background-color:#666;" onchange=submit()>
		{html_options values=$dbs output=$dbs selected=$dbs_now}
		</select>
		</td>
	{/if}
    </tr>
  </table>
</form>
-->
<!-- End Header -->