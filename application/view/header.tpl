<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" 
  "http://www.w3.org/TR/html4/loose.dtd">
<html lang="en">
<head>
<title>Bacula-Web</title>
<link rel="stylesheet" type="text/css" href="application/view/style/default.css">
<link rel="stylesheet" type="text/css" href="application/view/style/header.css">
<link rel="stylesheet" type="text/css" href="application/view/style/table.css">
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" >
</head>
<body>

<!-- Header -->
<div id="header">
 <div id="toolbar_top">
  <div class="toolbar_box right_box">
   <ul>
	<li> <a href="http://bugs.bacula-web.org" target="_blank" title="Bugs and features tracker">Bugs</a> </li>
	<li> <a href="http://www.bacula-web.org" target="_blank" title="Visit the official web site">About</a> </li>
	<li>Version 5.2.12</li>
   </ul>
  </div> 
  <div class="clear_both"></div>
 </div> <!-- end div toolbar_top -->

 <div id="header_main">
  <div class="toolbar_box left_box">
   <a href="index.php" title="Dashboard"> <img src="application/view/style/images/home_w.png" alt=""> </a>
  </div>
  <div class="toolbar_box right_box">
   <div class="app_name">Bacula-Web</div>
  </div>
  <div class="clear_both"></div>
 </div>

<div id="top_controls">
  <div class="toolbar_box right_box">
   <!-- Condifitional catalog selection if more than 1 catalog is defined in the configuration -->
   {if $catalog_nb > 1}
	<form class="catalog_selector" method="post" action="index.php">
	 Catalog {html_options name=catalog_id options=$catalogs selected=$catalog_current_id onchange="submit();"}
	</form>
   {/if}
  </div>
  <div class="clear_both"></div>
</div>
<!-- end Top controls -->

</div> <!-- end header -->