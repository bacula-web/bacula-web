<div class="container">
  <div class="page-header">
    <h3>{$page_name} <small>{t}Bacula director(s) overview{/t}</small></h3>
  </div>

  <p>Found {$directors_count} Bacula director catalog(s) in your configuration</p>

  <div class="row">
    {foreach from=$directors key=id item=director}
    <div class="col-xs-4">
      <div class="page-header"> <h4>{$director.label}</h4> <small>{$director.description}</small> </div>
      <div class="panel panel-default">
        <div class="panel-body">
          <h5>{t}Client(s){/t} <span class="label label-primary">{$director.clients}</span></h5>
          <h5>{t}Job(s){/t} <span class="label label-primary">{$director.jobs}</span></h5>
          <h5>{t}Total bytes{/t} <span class="label label-primary">{$director.totalbytes}</span></h5>
          <h5>{t}Total files{/t} <span class="label label-primary">{$director.totalfiles}</span></h5>
          <h5>{t}Database size{/t} <span class="label label-primary">{$director.dbsize}</span></h5>
          <h5>{t}Volume(s){/t} <span class="label label-primary">{$director.volumes}</span></h5>
          <h5>{t}Volume(s) size{/t} <span class="label label-primary">{$director.volumesize}</span></h5>
          <h5>{t}Pool(s){/t} <span class="label label-primary">{$director.pools}</span></h5>
          <h5>{t}FileSet(s){/t} <span class="label label-primary">{$director.filesets}</span></h5>
        </div>
      </div>
    </div>
    {/foreach}
  </div>
</div> <!-- end div class=container -->
