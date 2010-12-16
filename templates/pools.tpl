<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
  "http://www.w3.org/TR/html4/loose.dtd">
<html lang="en">
<head>
<title>bacula-web</title>
<link rel="stylesheet" type="text/css" href="style/default.css">
{literal}
<script type="text/javascript">
        function OpenWin(URL,wid,hei) {
                window.open(URL,"window1","width="+wid+",height="+hei+",scrollbars=yes,menubar=no,location=no,resizable=no")
        }
</script>
{/literal}

</head>
<body>
{popup_init src='./external_packages/js/overlib.js'}
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
			<td class="info" width="120px">Name</td>
			<td class="info" width="120px">{t}Bytes{/t}</td>
			<td class="info" width="120px">{t}Media Type{/t}</td>
			<td class="info" width="140px">{t}Expire{/t}</td>
			<td class="info" width="140px">{t}Last written{/t}</td>
			<td class="info">{t}Status{/t}</td>
		</tr>
	</table>
	
	<div class="listbox">
		<table class="list" border="0">
			{foreach from=$pool item=volume}
			<tr>
				<td width="120px">{$volume.volumename}</td>
				<td width="120px">{$volume.volbytes}</td>
				<td width="120px">{$volume.mediatype}</td>
				<td width="140px">{$volume.expire}</td>
				<td width="140px">{$volume.lastwritten}</td>
				<td>{$volume.volstatus}</td>
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
