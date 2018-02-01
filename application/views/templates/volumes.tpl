<div class="container">

  <div class="page-header">
    <h3>{$page_name} <small>{t}Bacula volume(s) overview {/t}</small></h3>
  </div>

  <div class="row">
    <div class="col-xs-12 col-md-8">
    </div>

    <div class="col-xs-12 col-md-4">
      <!-- Options -->
      <form class="form-inline" role="form" action="volumes.php" method="post">
        <span class="help-block">{t}Options{/t}</span>
        <div class="form-group">
          <label>{t}Order by{/t}</label>
          {html_options class="form-control input-sm" name=orderby options=$orderby selected=$orderby_selected}
        </div>
        <div class="checkbox">
          <label>
          <input type="checkbox" name="orderby_asc" value="{t}Asc{/t}" {$orderby_asc_checked}> Asc 
          </label>
        </div>
        <button type="submit" class="btn btn-primary btn-sm pull-right" title="{t}Apply filter and options{/t}">{t}Apply{/t}</button>
      </form>
     </div> <!-- end div class="col-x... -->
   </div> <!-- end div=class=row -->

   &nbsp;

   <div class="row">
     <div class="col-xs-12">
      <div class="table-responsive">
        <table class="table table-condensed table-striped text-center">
          <tr>
	    <th class="text-center">{t}Volume name{/t}</th>
	    <th class="text-center">{t}Bytes{/t}</th>
       <th class="text-center">{t}Jobs{/t}</th>
	    <th class="text-center">{t}Media Type{/t}</th>
	    <th class="text-center">{t}Pool{/t}</th>
	    <th class="text-center">{t}Expire{/t}</th>
	    <th class="text-center">{t}Last written{/t}</th>
	    <th class="text-center">{t}Status{/t}</th>
	    <th class="text-center">{t}Slot{/t}</th>
	    <th class="text-center">{t}In Changer{/t}</th>
	  </tr>
	  {foreach from=$volumes item=volume name=volumes}
     <tr>
       <td>{$volume.volumename}</td>
	    <td>{$volume.volbytes}</td>
	    <td>{$volume.voljobs}</td>
	    <td>{$volume.mediatype}</td>
	    <td>{$volume.pool_name}</td>
	    <td>{$volume.expire}</td>
	    <td>{$volume.lastwritten}</td>
	    <td title="{$volume.volstatus}">
         <i class="fa {$volume.status_icon}"></i>
       </td>
	    <td>{$volume.slot}</td>
	    <td>{$volume.inchanger}</td>
	  </tr>
     {foreachelse}
     <tr>
       <td colspan="10" class="text-center">
         {t}No volume(s) to display{/t}
       </td>
     </tr>
	  {/foreach}
    </table>
  </div> <!-- end div class="table-responsive" -->
  </div> <!-- end div class=col-xxx -->
</div> <!-- end div class=row -->

&nbsp;

<!-- Found volumes footer -->
<div class="row">
  <div class="col-xs-6">
    <div class="panel panel-default">
      <!-- Default panel contents -->
      <div class="panel-heading"><b>{t}Result{/t}</b></div>
      <div class="panel-body">
        <ul class="list-group">
          <li class="list-group-item">
            <span class="badge">{$volumes_count}</span>
            {t}Volume(s) found{/t}
          </li>
          <li class="list-group-item">
            <span class="badge">{$volumes_total_bytes}</span>
            {t}Total bytes{/t}
          </li>
        </ul>
      </div> <!-- end div class="panel-body -->
    </div> <!-- end div class="panel" -->
  </div> <!-- end div class="col-..." -->

</div> <!-- end div class="row" -->

</div> <!-- div class="container-fluid" -->

<!-- End pools.tpl -->
