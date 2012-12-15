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
	<table class="grid">
	  <tr>
		<th>{t}Time{/t}</th> 
		<th>{t}Event{/t}</th>
	  </tr>
	  {foreach from=$joblogs item=log}
	  <tr class="odd_even">
		<td width="150">{$log.time}</td>
		<td class="left_align">{$log.logtext}</td>		
	  </tr>
	  {/foreach}
	</table>
  </div> <!-- end div class=box --> 
  

</div> <!-- end div class=main_center -->

{include file="footer.tpl"}
