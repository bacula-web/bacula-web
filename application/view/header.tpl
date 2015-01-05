<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Bacula-Web - {$page_name}</title>

  <!-- Bootstrap front-end framework -->
  <link rel="stylesheet" href="vendor/bootstrap-3.3.1/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="vendor/bootstrap-3.3.1/dist/css/bootstrap-theme.min.css"> 
  <link rel="stylesheet" href="application/assets/css/default.css">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="vendor/font-awesome-4.2.0/css/font-awesome.min.css">

  <meta http-equiv="Content-Type" content="text/html;charset=utf-8" >
</head>

<body>

<!-- Header -->
<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
      <div class="container-fluid">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="index.php">Bacula-Web</a>
        </div> <!-- div class="navbar-header" -->
		
   	<!-- Reports dropdown menu -->
        <ul class="nav navbar-nav">
          <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">{t}Reports{/t} <span class="caret"></span></a>
            <ul class="dropdown-menu" role="menu">
 	      <li><a href="index.php">{t}Dashboard{/t}</a></li>
              <li><a href="jobs.php">{t}Jobs{/t}</a></li>
              <li><a href="pools.php">{t}Pools and volumes{/t}</a></li>
            </ul>
          </li>
        </ul>		

    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav navbar-right">
        <li class="dropdown">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown"> <span class="glyphicon glyphicon-cog"></span> </a>
          <ul class="dropdown-menu">
            <li role="presentation" class="dropdown-header">Tools</li>
            <li> 
              <a href="test.php" title="Display the test page"><i class="fa fa-wrench fa-fw"></i> {t}Test page{/t}</a>
            </li>
            <li role="presentation" class="divider"></li>
            <li role="presentation" class="dropdown-header">Help</li>
            <li> 
              <a href="http://www.bacula-web.org" title="Visit the official web site" target="_blank"><i class="fa fa-globe fa-fw"></i> {t}Official web site{/t}</a> 
            </li>
            <li> 
              <a href="http://bugs.bacula-web.org" title="Bug and feature request tracker" target="_blank"><i class="fa fa-bug fa-fw"></i> {t}Bug tracker{/t}</a> 
            </li>
            <li role="presentation" class="divider"></li>
            <li role="presentation" class="dropdown-header">{t}Version{/t}</li>
            <li class="disabled"> <a href="#"><i class="fa fa-info fa-fw"></i> Bacula-Web 7.0.1</a></li>
          </ul>
        </li>
      </ul>
    </div> <!-- div class="collapse navbar-collapse"-->
  </div> <!-- div class="container-fluid" -->
</div> <!-- class="navbar" -->

  <div class="container-fluid">
    <!-- Breadcrumb -->
    <div class="row">
      <div class="col-xs-8">
        <ol class="breadcrumb">
          <li class="active">{$page_name}</li>
            {php}
              $back     = null;
       	      $referrer = $_SERVER['HTTP_REFERER'];
              $referrer = end( explode( "/", $referrer) );

      	      $current  = $_SERVER['SCRIPT_FILENAME'];
              $current  = end( explode( "/", $current) );

              // If referrer and current are not equal and referrer isn't null/empty
              if( strcmp($referrer, $current) != 0  )
                $back = $referrer;

              // If current is Dashboard
      	      if( $current == 'index.php' )
                $back = null;

              if( !is_null($back) )
                echo "<li><a href='$back' title='{t}back to previous page{/t}'>Back</a></li>";
            {/php}
        </ol>
      </div> <!-- div class="col..." -->
    <div class="col-xs-4">
    <!-- Catalog selector -->
    {if $catalog_nb > 1}
    <div class="btn-group btn-group-sm pull-right">
      <button type="button" class="btn btn-primary"><i class="fa fa-database fa-fw"></i> {$catalog_label}</button>
      <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
        <span class="fa fa-caret-down"></span> 
      </button>
      <ul class="dropdown-menu">
        {foreach from=$catalogs key=catalog_id item=catalog_name}
          <li> <a href="index.php?catalog_id={$catalog_id}">
          {if $catalog_id eq $catalog_current_id} <i class="fa fa-check fa-fw"></i> {else} <i class="fa fa-fake fa-fw"></i> {/if}{$catalog_name}</a>
          </li>
        {/foreach}
      </ul>
    </div>
    {/if}
    <!-- end Catalog selector -->      
    </div> <!-- div class="col-xs..." -->
  </div> <!-- div class="row" -->
</div> <!-- div class="container" -->
