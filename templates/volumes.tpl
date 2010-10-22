<!-- volumes.tpl -->

<div class="box">
	<p class="title">Pools</p>
<!--
<table border=0 width=100% class=code cellspacing=0 cellpadding=0>
<tr align=center width=100%>
        <td class=tbl_header2 background="images/bg4.png"><b>Pools</b></td>
        <td class=tbl_header2 background="images/end4.png"><img src="images/empty.png"></td>
</tr>
-->
<table>
	{foreach from=$pools item=current}
		<tr>
			<td colspan=6>&nbsp;</td></tr>
        <tr>
			<td colspan="6" class="pool_name">
                {$current}
            </td>
        </tr>
        {foreach from=$volumes item=current2}
                {assign var=key value=$current2}
                {foreach from=$key item=current3 name=loop}
                {if $current3.3 == $current && $current3.0 != ""}
                        {if $smarty.foreach.loop.first == TRUE}
                                <tr>
                                        <td class="tbl_vol_header">Volume Name</td>
                                        <td class="tbl_vol_header">Volume Bytes</td>
                                        <td class="tbl_vol_header">Media Type</td>
                                        <td class="tbl_vol_header">Expire on</td>
                                        <td class="tbl_vol_header">Last Written</td>                                      
                                        <td class="tbl_vol_header">Volume Status</td>
                                </tr>
                        {/if}
                                <tr align=center bgcolor={cycle values="#D9E3FC,#CBE7F5"}>
                                        <td>{$current3.0}</td>
                                        <td>{$current3.1|fsize_format|default:0}</td>
                                        <td>{$current3.4}</td>
                                        <td {popup text='$current3.6}>
											{if $current3.6|date_format:"%Y" <= "1979"}
											  --
											{else}
											  {$current3.6|date_format:"%Y/%m/%d"}
											{/if}
										</td>
                                        <td {popup text='$current3.5}>
											{if $current3.5 == "0000-00-00 00:00:00"}
											  --
											{else}
											  {$current3.5|date_format:"%Y/%m/%d"}
											{/if}
										</td>
                                        <td>
                                                <font color=
                                                {if $current3.2 == "Error"}
                                                        red>
                                                {elseif $current3.2 == "Purged"}
                                                        blue>
                                                {elseif $current3.2 == "Append"}
                                                        green>
                                                {elseif $current3.2 == "Recycle"}
                                                        orange>
                                                {else}
                                                        "">
                                                {/if}
                                                {$current3.2}
                                                </font>
                                        </td>
                                </tr>
                        {/if}
                {/foreach}
        {/foreach}
{/foreach}
</table>

</div> <!-- end div box -->

<!-- End volumes.tpl -->
