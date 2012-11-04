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
	  <table class="test">
		{foreach from=$checks item=check}
		  <tr>
			<td><b>{$check.check_label}</b></td>
			<td rowspan="2" style="text-align: center;"> 
			  <img src='application/view/style/images/{$check.check_result}' width='23' alt=''/> </td>
		  </tr>
		  <tr>
			<td class="comment">{$check.check_descr}</td>
		  </tr>
		{/foreach}
	  <tr>
		<td> <b>Graph capabilites</b> </td>
	    <td rowspan="2" style="text-align: center;">
		  <img src="{$graph_test}" alt='' width="300" />
	    </td>
	  </tr>
	  <tr>
	    <td class="comment">{t}Graph system capabilities (Bacula-web only use PNG image format){/t}</td>
	  </tr>
	</table>
</div> <!-- end div id=main_center -->

</body>
</html>
