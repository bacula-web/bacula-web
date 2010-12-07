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
<div class="box">
  <p class="title">Pools</p>
	
  <table class="list">
	{foreach from=$pools item=pool key=pool_name}
	<tr>
		<th colspan="6" style="font-size: 10pt; text-align: center; background-color: #E0C8E5; color: black; padding: 3px;">
			{$pool_name}
		</th>
	</tr>
	<tr style="text-align: center;">
		<td class="info">Name</td>
		<td class="info">{t}Bytes{/t}</td>
		<td class="info">{t}Media Type{/t}</td>
		<td class="info">{t}Expire{/t}</td>
		<td class="info">{t}Last written{/t}</td>
		<td class="info">{t}Status{/t}</td>
	</tr>
	{foreach from=$pool item=volume}
		<tr style="text-align: center;">
			<td style="text-align: left;">{$volume.volumename}</td>
			<td>{$volume.volbytes}</td>
			<td>{$volume.mediatype}</td>
			<td>{$volume.expire}</td>
			<td>{$volume.lastwritten}</td>
			<td>{$volume.volstatus}</td>
		{foreachelse}
		<tr>
			<td colspan="6" style="text-align: center; font-weight: bold; font-size: 8pt; padding: 1em;">
				No volume in this pool
			</td>
		{/foreach}
		</tr>
	{/foreach}
  </table>

</div> <!-- end div box -->
</div> <!-- end div main_center -->

<!-- End volumes.tpl -->

{include file="footer.tpl"}
