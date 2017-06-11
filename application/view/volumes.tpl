{include file=header.tpl}

<div class="container">

  <div class="row">

    <div class="col-xs-12">

      <h3>{$page_name}</h3>

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
	    <td>{$volume.volstatus}</td>
	    <td>{$volume.slot}</td>
	    <td>{$volume.inchanger}</td>
	  </tr>
	  {/foreach}
    </table>
  </div> <!-- end div class="table-responsive" -->
  </div> <!-- end div class=col-xxx -->
  </div> <!-- end div class="row" -->

</div> <!-- div class="container-fluid" -->

<!-- End pools.tpl -->

{include file="footer.tpl"}
