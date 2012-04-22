{include file="header.tpl"}
{include file="choosePeriod.tpl" baseUrl="$zoneUrl/list"}
<table width="100%">
	<tr>
		<td valign="top" width="20%" class="zbg-blue10 zbor-9 zc-blue1 zbc-blue1" >
			Expense Actions<br>
			<a href="{$zoneUrl}/edit/new" class="zt-m">Add New Expense</a><br>
			<a href="{$zoneUrl}/import" class="zt-m">Import Expenses</a><br>
			<a href="{$zoneUrl}/storeCategories" class="zt-m">Set Automatic Category Assignments</a><br>
			<br>
			Budget Actions<br>
			<a href="{$SCRIPT_URL}/budget/editCat/new/{$year}/{$month}" class="zt-m">Add Budget Category</a><br>
			<a href="{$SCRIPT_URL}/budget/printPie/{$year}/{$month}" class="zt-m" target="new">View Pie Chart</a><br>
			<a href="{$SCRIPT_URL}/budget/printBarChart/{$year}/{$month}" class="zt-m" target="new">View Bar Chart</a><br>
			<a href="{$SCRIPT_URL}/budget/viewLineChart/{$year}/{$month}" class="zt-m" target="new">View Balance Chart</a><br>
			<br>
			Account Actions<br>
			<a href="{$SCRIPT_URL}/accounts/{$year}/{$month}/editAccounts" class="zt-m">Edit Accounts</a>
		</td>
		<td width="80%">
			<div id="search" class="zbb-sspace" style="width: 700px;">
				{foreach from=$searchParams key='name' item='param'}
					{include file="search.tpl"}
				{/foreach}
				<button name="search" onclick="submitForm('search');" value="Search">Go</button>
			</div>
			<div id="expenseList" class="zbb-sspace">
				{include file="expenseList.tpl"}
				{*{forms2 form=$form empty_error="No Expenses for this Month" sort_type="forms"}*}
			</div>
			<div id="budgetList" class="zbb-sspace">
{include file="budgetList.tpl"}
			</div>
		</td>
	</tr>
</table>

{include file="footer.tpl"}
