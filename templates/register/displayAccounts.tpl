Accounts
<table>
	<tr>
		<th>
			Name
		</th>
		<th>
			Balance
		</th>
	</tr>
	{foreach from=$accounts item="account"}
	<tr>
		<td style="border-bottom: solid black 1px">
			{$account.account}
		</td>
		<td style="border-bottom: solid black 1px">
			{$account.balance}
		</td>
	</tr>
	{/foreach}
	<tr>
		<td>
			<input type="text" name="name">
		</td>
		<td>
			<input type="text" name="balance"><input type="button" name="addaccount" value="Add Account" onclick="addAccount();">
		</td>
	</tr>
</table>