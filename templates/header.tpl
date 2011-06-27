<!-- Header -->
<div id="header">
 <p>bacula-web</p>
</div> <!-- end div header -->

<div id="subheader">
 <ul>
	<li> <a href="http://bacula-web.dflc.ch/bugs" target="_blank">Bugs</a> </li>
	<li> <a href="http://bacula-web.dflc.ch" target="_blank">About</a> </li>

	<!-- Condifitional catalog selection if more than 1 catalog is defined in the configuration -->
	{if $catalog_nb > 1}
	<li>
		<form method="post" action="index.php">
			Catalog&nbsp;
			<select name="catalog_id" OnChange="submit()">
				{html_options options=$catalogs selected=$catalog_current_id}
			</select>
		</form>
	</li>
	{/if}
 </ul>
</div> <!-- end div subheader -->

<!-- End Header -->
