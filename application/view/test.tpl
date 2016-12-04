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
			<div class="img_loader text-center">
				<i class="fa fa-spinner fa-spin fa-2x"></i>&nbsp;
				<p>Loading graph</p>
			</div>
			<img class="img-responsive center-block" src="{$bar_graph}" alt="Graph 1">
		</div>
		<div class="col-xs-12 col-sm-6">
			<div class="img_loader text-center">
				<i class="fa fa-spinner fa-spin fa-2x"></i>&nbsp;
				<p>Loading graph</p>
			</div>
			<img class="img-responsive center-block" src="{$pie_graph}" alt="Graph 2">
		</div>
	</div>
</div> <!-- div class="container" -->

{include file="footer.tpl"}
