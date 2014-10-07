{include file=header.tpl}

<div class="container-fluid">
    
  <h4>{t}Required components{/t}</h4>
	<table class="table table-striped">
		<tr>
			<th class="text-center">Component</th>
			<th class="text-center">Description</th>
			<th class="text-center">Status</th>
		</tr>
		{foreach from=$checks item=check}
		<tr>
			<td><strong>{$check.check_label}</strong></td>
			<td><i>{$check.check_descr}</i></td>
			<td class="text-center"> 
				<span class="glyphicon {$check.check_result}"></span> 
			</td>
		</tr>
		{/foreach}
		<!-- Graph testing -->
		<tr>
			<th colspan="3">Graph capabilites (png images format only)</th>
		</tr>
		<tr>
			<td>
				<img class="img-responsive" src="{$bar_graph}" alt='' />
			</td>
			<td colspan="2">
				<img class="img-responsive" src="{$pie_graph}" alt='' />
			</td>
		</tr>
	</table>

</div> <!-- div class="container-fluid" -->

{include file="footer.tpl"}
