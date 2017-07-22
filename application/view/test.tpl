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
	<h4>Graph capabilites <small>(png images format only)</small></h4>
	<div class="row">
		<div class="col-xs-12 col-sm-6"> 
         <div id="{$pie_graph_id}"> <svg></svg> </div>
         {$pie_graph} 
		</div>
		<div class="col-xs-12 col-sm-6">
         <div id="{$bar_chart_id}"> <svg></svg> </div>
         {$bar_chart}
		</div>
	</div>

</div> <!-- div class="container" -->

{include file="footer.tpl"}
