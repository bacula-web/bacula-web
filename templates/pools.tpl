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
  <a href="index.php" title="Back to the dashboard">Dashboard</a> > Pools and Volumes list
</div>

<div id="main_center">

{foreach from=$pools item=pool key=pool_name}
<div class="box">
	<p class="title">{$pool_name}</p>
	<table class="list" border="0">
		<tr>
			<td class="tbl_header" width="120">Name</td>
			<td class="tbl_header" width="120">{t}Bytes{/t}</td>
			<td class="tbl_header" width="120">{t}Media Type{/t}</td>
			<td class="tbl_header" width="140">{t}Expire{/t}</td>
			<td class="tbl_header" width="140">{t}Last written{/t}</td>
			<td class="tbl_header">{t}Status{/t}</td>
		</tr>
	</table>
	
	<div class="listbox">
		<table class="list" border="0">
			{foreach from=$pool item=volume}
			<tr>
				<td width="120" class="{$volume.class}">{$volume.volumename}</td>
				<td width="120" class="{$volume.class}">{$volume.volbytes}</td>
				<td width="120" class="{$volume.class}">{$volume.mediatype}</td>
				<td width="140" class="{$volume.class}">{$volume.expire}</td>
				<td width="140" class="{$volume.class}">{$volume.lastwritten}</td>
				<td class="{$volume.class}">{$volume.volstatus}</td>
			</tr>
			{foreachelse}
			<tr>
				<td colspan="6" style="text-align: center; font-weight: bold; font-size: 8pt; padding: 1em;">
					No volume(s) in this pool
				</td>
			</tr>
			{/foreach}
		</table>	
	</div>
</div> <!-- end div box-->
{/foreach}

</div> <!-- end div main_center -->

<!-- End volumes.tpl -->

{include file="footer.tpl"}
