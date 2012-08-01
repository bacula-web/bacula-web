{include file=header.tpl}

<div id="nav">
  <ul>
    <li>
      <a class="home" href="index.php" title="{t}Back to the dashboard{/t}">{t}Dashboard{/t}</a>
    </li>
    <li>{t}Job logs{/t}</li>
  </ul>
</div>

<div class="main_center">
  <h4>{t}Job id{/t} <b>{$jobid}</b></h4>
  
  <div class="box">
	<table>
	  <tr>
		<td class="tbl_header">{t}Time{/t}</td> 
		<td class="tbl_header">{t}Event{/t}</td>
	  </tr>
	  {foreach from=$joblogs item=log}
	  <tr>
		<td width="150" class="{$log.class}">{$log.time}</td>
		<td style="text-align: left" class="{$log.class}">{$log.logtext}</td>		
	  </tr>
	  {/foreach}
	</table>
  </div> <!-- end div class=box --> 
  

</div> <!-- end div class=main_center -->

{include file="footer.tpl"}
