{* Design of table of pools *}
<table border=0 width=100% class=code cellspacing=0 cellpadding=0>
<tr align=center width=100%>
        <td class=tbl_header2 background="images/bg4.png"><b>Pools</b></td>
        <td class=tbl_header2 background="images/end4.png"><img src="images/empty.png"></td>
</tr>
<tr><td colspan=2>
<table border=0 width=100% class=code cellspacing=0 cellpadding=0>
{foreach from=$pools item=current}
        <tr><td colspan=6>&nbsp;</td></tr>
        <tr>
                <th align=left style="background-color: #E0C8E5; color: black;" background="images/bg6.png">
                {$current}
                </th>
        </tr>
        {foreach from=$volumes item=current2}
                {assign var=key value=$current2}
                {foreach from=$key item=current3 name=loop}
                {if $current3.3 == $current && $current3.0 != ""}
                        {if $smarty.foreach.loop.first == TRUE}
                                <tr align=center background="images/bg5.png">
                                        <td background="images/bg5.png" class=tbl_pool_inter_1>{t}Volume Name{/t}</td>
                                        <td background="images/bg5.png" class=tbl_pool_inter_2>{t}Volume Bytes{/t}</td>
                                        <td background="images/bg5.png" class=tbl_pool_inter_2>{t}Media Type{/t}</td>
                                        <td background="images/bg5.png" class=tbl_pool_inter_2>{t}When expire?{/t}</td>
                                        <td background="images/bg5.png" class=tbl_pool_inter_2>{t}Last Written{/t}</td>                                      
                                        <td background="images/bg5.png" class=tbl_pool_inter_3>{t}Volume Status{/t}</td>
                                </tr>
                        {/if}
                                <tr align=center bgcolor={cycle values="#D9E3FC,#CBE7F5"}>
                                        <td>{$current3.0}</td>
                                        <td>{$current3.1|fsize_format|default:0}</td>
                                        <td>{$current3.4}</td>
                                        <td {popup text="$current3.6}">{if $current3.6|date_format:"%Y" <= "1979"}--{else}{$current3.6|date_format:"%Y/%m/%d"}{/if}</td>
                                        <td {popup text="$current3.5}">{if $current3.5 == "0000-00-00 00:00:00"}--{else}{$current3.5|date_format:"%Y/%m/%d"}{/if}</td>
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
</td></tr></table>
