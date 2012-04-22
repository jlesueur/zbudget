<script type='text/javascript'>
{literal}
function deleteAccount(id)
{
	row = document.getElementById('accountRow' + id);
	document.main_form.account_id.value = id;
	dojo.io.bind({
		url:{/literal}
			"{$zoneUrl}/deleteAccount",
		{literal}
		mimetype: 'text/json',
		method: 'post',
		formNode: document.main_form,
		handler: function(type, data, e){if(data)deleteAccountRow(row);}
		});
}

function deleteAccountRow(row)
{
	row.parentNode.removeChild(row);
}

function addAccount()
{
	dojo.io.bind({
		url:{/literal}
			"{$zoneUrl}/addAccount",
		{literal}
		mimetype: 'text/json',
		method: 'post',
		formNode: document.main_form,
		handler: function(type, data, e){ addAccountRow(data);}
		});
}

function addAccountRow(data)
{
	alert(data);
	tr = document.createElement('tr');
	tr.id = 'accountRow' + data['id'];
	td = document.createElement('td');
	td.style.borderBottom = 'solid black 1px';
	td.innerHTML = data['account'];
	tr.appendChild(td);
	td = document.createElement('td');
	td.style.borderBottom = 'solid black 1px';
	td.innerHTML = data['balance'];
	tr.appendChild(td);
	td = document.createElement('td');
	td.style.borderBottom = 'solid black 1px';
	td.innerHTML = "<div style=\"float: right\"><input type=\"button\" name=\"deleteaccount\" value=\"Delete\" onclick=\"deleteAccount(" + data['id'] + ");\"></div>";
	tr.appendChild(td);
	row = document.getElementById('addRow');
	row.parentNode.insertBefore(tr, row);
}
{/literal}
</script>
<input type="hidden" name="account_id">
Accounts
<table>
	<tr>
		<th>
			Name
		</th>
		<th>
			Number
		<th>
		<th>
			Balance
		</th>
	</tr>
	{foreach from=$accounts item="account"}
	<tr id="accountRow{$account.id}">
		<td style="border-bottom: solid black 1px">
			<input type="text" name="account[{$account.id}][name]" value="{$account.account}">
		</td>
		<td style="border-bottom: solid black 1px">
			<input type="text" name="account[{$account.id}][number]" value="{$account.number}">
		</td>
		<td style="border-bottom: solid black 1px">
			<input type="text" name="account[{$account.id}][balance]" value="{$account.balance}">
		</td>
		<td style="border-bottom: solid black 1px">
			<div style="float: right">
				<input type="button" name="deleteaccount" value="Delete" onclick="deleteAccount({$account.id});">
			</div>
		</td>
	</tr>
	{/foreach}
	<tr id="addRow">
		<td>
			<input type="text" name="name">
		</td>
		<td>
			<input type="text" name="number">
		</td>
		<td>
			<input type="text" name="balance">
		</td>
		<td>
			<input type="button" name="addaccount" value="Add Account" onclick="addAccount();">
		</td>
	</tr>
</table>
<input type="button" name="update" value="Update" onclick="submitForm('update');">