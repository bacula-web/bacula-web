<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Bacula-Web - {$page_name}</title>

  <!-- Bootstrap front-end framework -->
  <link rel="stylesheet" type="text/css" href="application/assets/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" type="text/css" href="application/assets/css/default.css">
  <script src="application/assets/jquery/jquery-1.11.1.min.js"></script>
  <script src="application/assets/bootstrap/js/bootstrap.min.js"></script>

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
			<ul class="nav navbar-nav navbar-right">
	{if $catalog_nb > 1}
	<!-- Bacula catalog dropdown -->
	<li>
		<form class="navbar-form" action="index.php" method="post">
			<select name="catalog_id" class="form-control">
				{foreach from=$catalogs key=catalog_id item=catalog_name}
					<option value="{$catalog_id}"
						{if $catalog_id eq $catalog_current_id} selected {/if} >
							{$catalog_name}
					</option>
				{/foreach}
			</select>

			<button type="submit" class="btn btn-success">Use</button>
			{$catalog_selected_id}
		</form>
	</li>
	{/if}			
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown"> <span class="glyphicon glyphicon-cog"></span> </a>
					<ul class="dropdown-menu">
						<li role="presentation" class="dropdown-header">Tools</li>
						<li> <a href="test.php" title="Test page">Test page</a></li>
						<li role="presentation" class="divider"></li>
						<li role="presentation" class="dropdown-header">Help</li>
						<li> <a href="http://www.bacula-web.org" target="_blank">Official web site</a> </li>
						<li> <a href="http://bugs.bacula-web.org" target="_blank">Bug tracker</a> </li>
						<li role="presentation" class="divider"></li>
						<li role="presentation" class="dropdown-header">Version</li>
						<li class="disabled"> <a href="#">Bacula-Web 6.0.1</a></li>
					</ul>
				</li>
			</ul>
		</div> <!-- div class="collapse navbar-collapse"-->
  </div> <!-- div class="container-fluid" -->
</div> <!-- class="narvar" -->


<div class="container-fluid">
  <!-- Breadcrumb -->
  <ol class="breadcrumb">
	<li class="active">{$page_name}</li>
  {php} 
	  $back	    = null;
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
   	    echo "<li><a href='$back' title='back to previous page'>Back</a></li>";
  {/php}
  </ol>
  
  <h3>{$page_name}</h3>
</div> <!-- div class="container" -->
