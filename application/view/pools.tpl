{include file=header.tpl}

<div class="container-fluid">
  <div class="row">
    <div class="col-md-10">
    {foreach from=$pools item=pool key=pool_name}
	<h4>{$pool_name}</h4>
	<table class="table table-bordered table-striped table-hover text-center">
		<tr>
			<th class="text-center">{t}Volume name{/t}</th>
			<th class="text-center">{t}Bytes{/t}</th>
			<th class="text-center">{t}Media Type{/t}</th>
			<th class="text-center"title="{t}Estimated expiration date{/t}">{t}Expire{/t}</th>
			<th class="text-center">{t}Last written{/t}</th>
			<th class="text-center">{t}Status{/t}</th>
		</tr>
		{foreach from=$pool item=volume}
		<tr>
			<td class="strong">{$volume.volumename}</td>
			<td>{$volume.volbytes}</td>
			<td>{$volume.mediatype}</td>
			<td>{$volume.expire}</td>
			<td>{$volume.lastwritten}</td>
			<td>{$volume.volstatus}</td>
		</tr>
		{foreachelse}
		<tr>
			<td colspan="6" class="text-center">
				{t}No volume(s) in this pool{/t}
			</td>
		</tr>
		{/foreach}
	</table>	
    {/foreach}
    </div> <!-- div class="col-md-...." -->
  </div> <!-- div class="row" -->
</div> <!-- div class="container-fluid" -->

<!-- End pools.tpl -->

{include file="footer.tpl"}
