{include file=header.tpl}

<div class="container-fluid">

    <h3>{$page_name}</h3>
  
    <div class="panel panel-default">
		<div class="panel-heading"> <h4 class="panel-title">{t}Job details{/t}</h4> </div>
		<div class="panel-body">
			<dl class="dl-horizontal">
				<dt>{t}Job id{/t}</dt> <dd>{$jobid}</dd>
			</dl>
		</div> <!-- end div class="panel-body" -->
	</div>
	<div class="table-responsive">
    <table class="table table-hover table-striped table-condensed table-bordered">
		<tr>
			<th class="text-center">{t}Time{/t}</th> 
			<th class="text-center">{t}Event{/t}</th>
		</tr>
		{foreach from=$joblogs item=log}
	    <tr class="{$log.class}">
			<td class="text-center">{$log.time}</td>
			<td class="text-left">{$log.logtext}</td>		
	    </tr>
        {foreachelse}
        <tr>
			<td colspan="2" class="text-center">{t}No log(s) for this job{/t}</td>
        </tr>
		{/foreach}
	</table>
	</div>
</div> <!-- end div class="container-fluid" -->

{include file="footer.tpl"}