Budget Categories
<table>
	<tr>
		<th>
			Name
		</th>
		<th>
			Amount Budgeted
		</th>
	</tr>
	{foreach from=$categories item="category"}
	<tr class="{$category.color}">
		<td class="{$category.color}">
			{$category.name}
		</td>
		<td class="{$category.color}">
			{$category.amount}
		</td>
	</tr>
	{/foreach}
	<tr>
		<td>
			<input type="text" name="name">
			<select name="color">
				{html_options options=$colors selected=$categoryInfo.color}
			</select>
		</td>
		<td>
			<input type="text" name="amount"><input type="button" name="addaccount" value="Add Category" onclick="addCategory();">
		</td>
	</tr>
</table>