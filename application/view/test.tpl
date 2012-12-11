{include file=header.tpl}

<div id="nav">
  <ul>
    <li>
      <a class="home" href="index.php" title="{t}Back to the dashboard{/t}">{t}Dashboard{/t}</a>
    </li>
    <li>{t}Test page{/t}</li>
  </ul>
</div>

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
			<td class="left strong">{$check.check_label}</td>
			<td class="left">{$check.check_descr}</td>
			<td > 
				<img src='application/view/style/images/{$check.check_result}' width='23' alt=''/> 
			</td>
		</tr>
		{/foreach}
		<!-- Graph testing -->
		<tr>
			<td class="left"> <b>Graph capabilites</b> </td>
			<td colspan="2">
				<img src="{$graph_test}" alt='' width="300" />
			</td>
		</tr>
		<tr>
			<td colspan="3" class="comment">{t}Graph system capabilities (Bacula-web only use PNG image format){/t}</td>
		</tr>
	</table>
</div> <!-- end div id=main_center -->

</body>
</html>
