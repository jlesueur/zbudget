{include file="header.tpl"}
<h1 class="zbg-blue10 zbc-blue1 zc-blue1 zbor-10 zt-tah zt-mxl zbb-sspace" style="margin: 0px; text-align: center;">
	<a class="zc-blue7" href="#" onclick="submitForm('prevStart');return false;">&lt;</a>
	{$strings.months.$startMonth} {$startYear}
	{if $startMonth == $endMonth && $startYear == $endYear}
	{else}
	<a class="zc-blue7" href="#" onclick="submitForm('nextStart'); return false;">&gt;</a>
	{/if}
	to 
	{if $startMonth == $endMonth && $startYear == $endYear}
	{else}
	<a class="zc-blue7" href="#" onclick="submitForm('prevEnd'); return false;">&lt;</a>
	{/if}
	{$strings.months.$endMonth} {$endYear}
	<a class="zc-blue7" href="#" onclick="submitForm('nextEnd'); return false;">&gt;</a>
</h1>
<table width="100%">
	<tr>
		<td valign="top" width="150" class="zbg-blue10 zbor-9 zc-blue1 zbc-blue1" >
			
		</td>
		<td width="700">
			<input type="button" onclick="window.location.href='{$zoneUrl}/printLineChart/{$startYear}/{$startMonth}/{$endYear}/{$endMonth}';" value="Print" style="float:right">
			<img src="{$zoneUrl}/imageLineChart/{$startYear}/{$startMonth}/{$endYear}/{$endMonth}">
		</td>
	</tr>
</table>
{include file="footer.tpl"}