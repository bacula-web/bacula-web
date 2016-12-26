{include file=header.tpl}

<div class="container-fluid" id="testpage">

    <h3>{$page_name} <small>{t}Required components{/t}</small></h3>
    
	<table class="table table-striped">
		<tr>
			<th class="text-center">Status</th>
			<th class="text-center">Component</th>
			<th class="text-center">Description</th>
		</tr>
		{foreach from=$checks item=check}
		<tr>
			<td class="text-center"> <span class="glyphicon {$check.check_result}"></span> </td>
			<td>{$check.check_label}</td>
			<td class="text-center"> 
		        <span class="glyphicon glyphicon-info-sign" aria-hidden="true" data-toggle="tooltip" data-placement="left" title="{$check.check_descr}"></span>
			</td>
		</tr>
		{/foreach}
	</table>
	
	<!-- Graph testing -->
	<h4>Graph capabilites</h4>
	<div class="row">
		<div class="col-xs-12 col-sm-6"> 
			<div id="bar_graph"><i class="fa fa-spinner fa-spin fa-2x"></i>&nbsp;{t}Loading{/t}...</div>
			{literal}
			<script type="text/javascript">
			$(function () {
			{/literal}
			    {$bar_graph_js}
			{literal}
			});
			</script>
			{/literal}
		</div>
		<div class="col-xs-12 col-sm-6">
			<div id="pie_graph"><i class="fa fa-spinner fa-spin fa-2x"></i>&nbsp;{t}Loading{/t}...</div>
			{literal}
			<script type="text/javascript">
			$(function () {
			{/literal}
			    {$pie_graph_js}
			{literal}
			});
			</script>
			{/literal}
		</div>
	</div>
</div> <!-- div class="container" -->

{include file="footer.tpl"}
