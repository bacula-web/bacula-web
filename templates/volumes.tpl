<!-- volumes.tpl -->

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

<!-- End volumes.tpl -->
