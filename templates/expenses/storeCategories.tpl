{include file="header.tpl"}
<div class="zbg-blue10 zbc-blue1 zc-blue1 zbor-10 zt-tah zt-mxl" style="font-weight: bold; padding: 3px">
	<div style="text-align:center;">
		Update Automatic Category Assignments
	</div>
</div>
<table width="100%">
	<tr>
		<td valign="top" width="150" class="zbg-blue10 zbor-9 zc-blue1 zbc-blue1" >
			<a href="{$zoneUrl}/list" class="zt-m">View Expenses</a><br/>
			<a href="{$SCRIPT_URL}/budget/editCat/new/{$year}/{$month}" class="zt-m">Add Budget Category</a><br/>
			<br/>
		</td>
		<td width="700">
			These rules are applied in order. The first rule that matches will be applied to the transaction.
			When you are ready to apply the rules to any expenses that have not yet been assigned to a category, click "Apply".
			<div id="storeCategories">
				{include file="includes/storeCategories.tpl"}
				<input type="button" onclick="submitForm('apply');" value="Apply">
			</div>
		</td>
	</tr>
</table>
{include file="footer.tpl"}