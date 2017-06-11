{include file=header.tpl}

<div class="container">

  <h3>{$page_name}</h3>

  <div class="col-xs-12">
    <div class="table-responsive">
      <table class="table table-condensed table-striped text-center">
        <tr>
	  <th class="text-center">{t}Pool name{/t}</th>
	  <th class="text-center">{t}Volume count{/t}</th>
	  <th class="text-center">{t}Total bytes{/t}</th>
	  <th class="text-center">{t}Volumes{/t}</th>
	</tr>
	{foreach from=$pools item=pool key=pool_name name=pools}
          <tr>
	    <td>{$pool.name}</td>
	    <td>{$pool.numvols}</td>
	    <td>{$pool.totalbytes}</td>
	    <td>
	      <a title="{t}Show volumes{/t}" class="btn btn-primary btn-sm" role="button" href="volumes.php?pool_id={$pool.poolid}">{t}Show Volumes{/t}</a>
	    </td>
	  </tr>
	{/foreach}

</div> <!-- div class="container-fluid" -->

<!-- End pools.tpl -->

{include file="footer.tpl"}
