<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Bacula-Web - {$page_name}</title>

  <!-- Bootstrap front-end framework -->
  <link rel="stylesheet" href="core/vendor/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="core/vendor/bootstrap/css/bootstrap-theme.min.css"> 
  <link rel="stylesheet" href="core/vendor/bootstrap-datetimepicker-4.7.14/css/bootstrap-datetimepicker.min.css">
  <link rel="stylesheet" href="application/assets/css/default.css">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="core/vendor/font-awesome/css/font-awesome.min.css">

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
		
		<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
			<!-- Reports dropdown menu -->
			<ul class="nav navbar-nav">
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-file-text-o fa-fw"></i> {t}Reports{/t} <span class="caret"></span></a>
					<ul class="dropdown-menu" role="menu">
						<li><a href="jobs.php">{t}Jobs{/t}</a></li>
						<li><a href="pools.php">{t}Pools and volumes{/t}</a></li>
					</ul>
				</li>
			</ul>		
		
			<ul class="nav navbar-nav navbar-right">
				<!-- Catalog selector -->
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-database fa-fw"></i> {$catalog_label} <span class="caret"></span></a>
						
					<ul class="dropdown-menu">
						{foreach from=$catalogs key=catalog_id item=catalog_name}
						<li><a href="index.php?catalog_id={$catalog_id}">
						{if $catalog_id eq $catalog_current_id} <i class="fa fa-check fa-fw"></i> {else} <i class="fa fa-fake fa-fw"></i> {/if}{$catalog_name}</a>
						</li>
						{/foreach}
					</ul>
				</li>
				<!-- end Catalog selector -->
				
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown"> <i class="fa fa-cog fa-fw"></i> <span class="hidden-sm hidden-md hidden-lg">{t}About{/t}</span></a>
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
						<li class="disabled"><a href="#"><i class="fa fa-info fa-fw"></i> Bacula-Web 7.4.0</a></li>
					</ul>
				</li>
			</ul>
		</div> <!-- div class="collapse navbar-collapse"-->
	</div> <!-- div class="container-fluid" -->
</div> <!-- class="navbar" -->

  <div class="container-fluid">
    <!-- Breadcrumb -->
    <div class="row">
      <div class="col-xs-12">
        <ol class="breadcrumb">
			{php}
				global $current_page;
				$scriptname = explode( "/", $_SERVER['SCRIPT_FILENAME']);
				$current = end( $scriptname );

				if( $current === 'index.php' ) {
					echo '<li class="active"> <i class="fa fa-home fa-fw"></i> Dashboard</li>';
				}else{
					echo '<li> <a href="index.php" title="{t}Back to Dashboard{/t}"><i class="fa fa-home fa-fw"></i> Dashboard</a> </li>';
					echo "<li class='active'>$current_page</li>";
				}
			{/php}
        </ol>
      </div> <!-- div class="col..." -->
  </div> <!-- div class="row" -->
</div> <!-- div class="container" -->
