<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" 
  "http://www.w3.org/TR/html4/loose.dtd">
<html lang="en">
<head>
<title>bacula-web</title>
<link rel="stylesheet" type="text/css" href="style/default.css">
<link rel="stylesheet" type="text/css" href="style/header.css">
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" >
</head>
<body>

<div id="toplinks">
 <ul>
	<li> <a href="http://www.bacula-web.org/bugs" target="_blank">Bugs</a> </li>
	<li> <a href="http://www.bacula-web.org" target="_blank">About</a> </li>

	<!-- Condifitional catalog selection if more than 1 catalog is defined in the configuration -->
	{if $catalog_nb > 1}
	<li>
		<form method="post" action="index.php">
			Select catalog&nbsp;
			{html_options name=catalog_id options=$catalogs selected=$catalog_current_id onchange="submit();"}
		</form>
	</li>
	{/if}
 </ul>
</div> <!-- end div toplinks -->

<!-- Header -->
<div id="header">
 <div id=header_appname>bacula-web</div>
</div> <!-- end div header -->

<!-- End Header -->
