{include file=header.tpl}

<div class="container-fluid">
  
  <div class="col-md-10 col-lg-10">

    <h4>{t}Job id{/t} <b>{$jobid}</b></h4>

    <table class="table table-hover table-striped table-condensed table-bordered">
      <tr>
	<th class="text-center">{t}Time{/t}</th> 
	<th class="text-center">{t}Event{/t}</th>
	  </tr>
	  {foreach from=$joblogs item=log}
	    <tr class="{$log.class}">
	      <td class="text-center">{$log.time}</td>
	      <td class="text-left">{$log.logtext}</td>		
	    </tr>
          {foreachelse}
            <tr>
              <td colspan="2" class="text-center">{t}No log(s) for this job{/t}</td>
            </tr>
	  {/foreach}
	</table>
  </div> <!-- end div class="col-md-..." --> 

</div> <!-- end div class="container-fluid" -->

{include file="footer.tpl"}
