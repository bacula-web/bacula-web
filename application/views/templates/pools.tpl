<div class="container">

  <div class="page-header">
    <h3>{$page_name} <small>{t}Bacula pool(s) overview{/t}</small></h3>
  </div>

  <div class="row">
    <div class="col-xs-12">
      <div class="table-responsive">
        <table class="table table-condensed table-striped text-center paginate">
          <thead>
            <tr>
	          <th class="text-center">{t}Pool name{/t}</th>
	          <th class="text-center">{t}Volume count{/t}</th>
	          <th class="text-center">{t}Total bytes{/t}</th>
	          <th class="text-center">{t}Volumes{/t}</th>
	          </tr>
            </thead>
	      {foreach from=$pools item=pool key=pool_name name=pools}
            <tr>
	          <td>{$pool.name}</td>
	          <td>{$pool.numvols}</td>
	          <td>{$pool.totalbytes}</td>
	          <td>
	            <a title="{t}Show volumes{/t}" class="btn btn-primary btn-sm {if $pool.numvols == '0'} disabled {/if}" role="button" href="index.php?page=volumes&pool_id={$pool.poolid}">{t}Show Volumes{/t}</a>
	          </td>
	        </tr>
	      {/foreach}
	    </table>

      </div> <!-- end div class=table-responsive -->

    </div> <!-- end div class=col- -->

  </div> <!-- end div class="row" -->

</div> <!-- div class="container-fluid" -->

<!-- End pools.tpl -->
