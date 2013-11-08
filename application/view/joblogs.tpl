{include file=header.tpl}

<div class="main_center">
  <h4>{t}Job id{/t} <b>{$jobid}</b></h4>
  
  <div class="box">
	<table class="grid">
	  <tr>
		<th>{t}Time{/t}</th> 
		<th>{t}Event{/t}</th>
	  </tr>
	  {foreach from=$joblogs item=log}
	  <tr class="{$log.class}">
		<td width="150">{$log.time}</td>
		<td class="left_align">{$log.logtext}</td>		
	  </tr>
          {foreachelse}
            <tr>
              <td colspan="2">{t}No log(s) for this job{/t}</td>
            </tr>
	  {/foreach}
	</table>
  </div> <!-- end div class=box --> 
  

</div> <!-- end div class=main_center -->

{include file="footer.tpl"}
