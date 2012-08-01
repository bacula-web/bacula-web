{include file=header.tpl}

<div id="nav">
  <ul>
    <li>
      <a class="home" href="index.php" title="{t}Back to the dashboard{/t}">{t}Dashboard{/t}</a>
    </li>
    <li>{t}Pools and Volumes list{/t}</li>
  </ul>
</div>

{foreach from=$pools item=pool key=pool_name}
<div class="main_center">
	<div class="header">{$pool_name}</div>
	<div class="box">
	<table border="0">
		<tr>
			<td class="tbl_header" width="120">{t}Volume name{/t}</td>
			<td class="tbl_header" width="120">{t}Bytes{/t}</td>
			<td class="tbl_header" width="120">{t}Media Type{/t}</td>
			<td class="tbl_header" width="140">{t}Expire{/t}</td>
			<td class="tbl_header" width="140">{t}Last written{/t}</td>
			<td class="tbl_header">{t}Status{/t}</td>
		</tr>
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
			<td colspan="6" style="text-align: center; font-weight: bold; font-size: 8pt; padding: 1em; width: 100%;">
				{t}No volume(s) in this pool{/t}
			</td>
		</tr>
		{/foreach}
	</table>	
  </div> <!-- end div box-->
</div> <!-- end div main_center -->
{/foreach}

<!-- End pools.tpl -->

{include file="footer.tpl"}
