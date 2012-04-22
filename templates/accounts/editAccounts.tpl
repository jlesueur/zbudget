{include file="header.tpl"}
<table>
	<tr>
		<td valign="top" width="150" class="zbg-blue10 zbor-9 zc-blue1 zbc-blue1" >
			Expense Actions<br>
			<a href="{$SCRIPT_URL}/expenses/{$year}/{$month}/list" class="zt-m">View Expenses</a><br>
		</td>
		<td width="80%">
			{include file="includes/displayAccounts.tpl"}
		</td>
	</tr>
</table>
{include file="footer.tpl"}