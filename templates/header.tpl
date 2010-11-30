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
	<!-- Condifitional catalog selection if more than 1 catalog is defined in the configuration -->
	{if $dbs > 1}
	<li>
		<form method=post action=index.php>
			Catalog&nbsp; <select name=sel_database OnChange=submit()> {html_options values={$dbs} name=$selected_db=$dbs_now} </select>
		</form>
	</li>
	{/if}
 </ul>
</div> <!-- end div subheader -->

<!-- End Header -->