<table width=100% border=0>
 <tr><td align=center colspan=3 class=titulo background="images/bg8.png">
	{t}SELECT NEW REPORT{/t}
 </td></tr>
 <tr class=table1><td align=left>
	<form method=get action=report.php {if !isset($smarty.get.default) }target=_blank{/if}>
	<input type=hidden name=default value=2>
	{t}Select a job:{/t}
	</td>
	<td align=right>
	<select name=server>
		{html_options values=$total_name_jobs output=$total_name_jobs selected=$smarty.get.server} 
	</select>
 </td>
 </tr>
 <tr class=table2>
 <td align=left>
	{t}Graph mode:{/t}
 </td>
 <td align=right>
	<select name="modo_graph">
	<option value="lines">{t}lines{/t}</option>
	<option value="linepoints">{t}linepoints{/t}</option>
	<option value="points">{t}points{/t}</option>
	<option value="bars" selected>{t}bars{/t}</option>
	<option value="area">{t}area{/t}</option>
	</select>
	
 </td>
 
 </tr>
 <tr class=table1>
	<td>
	{t}Data to show:{/t}
	</td>
	<td align=right>
	<select name="tipo_dato">
	<option value="3" selected>{t}Transferred bytes{/t}</option>
	</select>
	</td>	
 </tr>
 <tr class=table2>
  <td align=left>
	{t}Start period:{/t}
  </td>
  <td align=right>
 {html_select_date prefix="StartDate" time=$time2 field_order="DMY" start_year="-1" end_year="+1" display_days=true}
  </td>
 </tr>
 <tr class=table1>
  <td align=left>{t}End period:{/t}
  </td>
  <td align=right>
  {html_select_date prefix="EndDate" time=$time field_order="DMY" start_year="-1" end_year="+1" display_days=true}
  </td>
 </tr>
 <tr>	
	<td colspan=3 align=center>
	<input type=submit value="{t}Create report{/t}">
	</form>
	</td>
 </tr>
 </table>