{include file=header.tpl}

<div class="main_center">
    
  <div class="header">{t}Required components{/t}</div>
	<table>
		<tr>
			<th>Component</th>
			<th>Description</th>
			<th>Status</th>
		</tr>
		{foreach from=$checks item=check}
		<tr>
			<td class="left_align strong">{$check.check_label}</td>
			<td class="left_align"><i>{$check.check_descr}</i></td>
			<td > 
				<img src='application/view/style/images/{$check.check_result}' width='23' alt=''/> 
			</td>
		</tr>
		{/foreach}
		<!-- Graph testing -->
		<tr>
			<th colspan="3">Graph capabilites (png images format only)</th>
		</tr>
		<tr>
			<td>
				<img src="{$bar_graph}" alt='' width="300" />
			</td>
			<td colspan="2">
				<img src="{$pie_graph}" alt='' width="300" />
			</td>
		</tr>
	</table>
</div> <!-- end div id=main_center -->

{include file="footer.tpl"}
