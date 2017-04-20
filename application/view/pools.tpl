{include file=header.tpl}

<div class="container-fluid">

	<h3>{$page_name}</h3>

	<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
    {foreach from=$pools item=pool key=pool_name name=pools}
    <div class="panel panel-default">
	    <div class="panel-heading" role="tab" id="heading{$pool_name}">
	      <h4 class="panel-title">
	        <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse{$pool_name}" aria-expanded="true" aria-controls="collapse{$pool_name}" class="btn-block">
	          {$pool_name} <span class="hidden-xs">-</span> <br class="hidden-sm hidden-md hidden-lg" /> <small><b>{t}Volumes{/t} / {t}Bytes{/t}:</b> {$pool.volumes|@count} / {$pool.total_used_bytes}</small> <span class="caret"></span>
	        </a>
	      </h4>
	    </div>
	    <div id="collapse{$pool_name}" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading{$pool_name}">
	      <div class="panel-body">
		  		<div class="table-responsive">
					<table class="table table-bordered table-striped table-hover text-center">
						<tr>
							<th class="text-center">{t}Volume name{/t}</th>
							<th class="text-center">{t}Bytes{/t}</th>
							<th class="text-center">{t}Media Type{/t}</th>
							<th class="text-center"title="{t}Estimated expiration date{/t}">{t}Expire{/t}</th>
							<th class="text-center">{t}Last written{/t}</th>
							<th class="text-center">{t}Status{/t}</th>
							<th class="text-center">{t}Slot{/t}</th>
							<th class="text-center">{t}In Changer{/t}</th>
						</tr>
						{foreach from=$pool.volumes item=volume}
						<tr>
							<td class="strong">{$volume.volumename}</td>
							<td>{$volume.volbytes}</td>
							<td>{$volume.mediatype}</td>
							<td>{$volume.expire}</td>
							<td>{$volume.lastwritten}</td>
							<td>{$volume.volstatus}</td>
							<td>{$volume.slot}</td>
							<td>{$volume.inchanger}</td>
						</tr>
						{foreachelse}
						<tr>
							<td colspan="7" class="text-center">
								{t}No volume(s) in this pool{/t}
							</td>
						</tr>
						{/foreach}
						<tr>
							<td><b>{t}Total{/t}</b></td>
							<td>{$pool.total_used_bytes}</td>
						</tr>
					</table>
				</div>
	      </div>
	    </div>
    </div>
    {/foreach}
	</div>
</div> <!-- div class="container-fluid" -->

<!-- End pools.tpl -->

{include file="footer.tpl"}
