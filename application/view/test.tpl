{include file=header.tpl}

<div class="container-fluid">
    
  <h4>{t}Required components{/t}</h4>
	<table class="table table-striped">
		<tr>
			<th>Component</th>
			<th>Description</th>
			<th>Status</th>
		</tr>
		{foreach from=$checks item=check}
		<tr>
			<td><strong>{$check.check_label}</strong></td>
			<td><i>{$check.check_descr}</i></td>
			<td> 
				<img class="img-responsive" src='application/view/style/images/{$check.check_result}' width='23' alt=''/> 
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
