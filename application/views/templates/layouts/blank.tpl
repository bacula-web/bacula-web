<!doctyle html>
<html lang="en">
<head>
    {include file="html-head.tpl"}
    {block name=title}{/block}
</head>

<body>
{*
<div class="container">
    {include file='flash.tpl'}
</div>
*}
<div class="wrapper d-flex flex-column min-vh-100 bg-light">
    {block name=body}{/block}
</div>

</body>
{include file="html-footer.tpl"}
</html>