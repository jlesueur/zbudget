<script>
{literal}
function changeCategory(expenseId, obj)
{
	row = document.getElementById('expenseRow' + expenseId);
	cell = document.getElementById('expenseCatCell' + expenseId);
	updater = dojo.io.bind({
				url:
				{/literal}
					"{$zoneUrl}/changeExpenseCategory/" + expenseId + "/" + obj.value,
				{literal}
				mimetype:'text/json',
				method: 'post',
				formNode: document.main_form,
				handler: function(type, data, e){updateRowCategory(row, data, cell);}
			});
}

function updateRowCategory(row, data, cell)
{
	cells = row.childNodes;
	row.className = data['color'];
	for(i in cells)
	{
		cells[i].className = data['color'];
	}
	budgetObj = document.getElementById('budgetList');
	if(budgetObj)
	{
		dojo.io.bind({
			url:
			{/literal}
				"{$zoneUrl}/budgetList",
			{literal}
			method: 'get',
			handler: function(type, data, e){updateBudgetList(data);}
			});
		//dojo.topics.publish();
	}
}

function updateBudgetList(data)
{
	budgetObj = document.getElementById('budgetList');
	//alert(budgetObj.innerHTML);
	//alert(data);
	budgetObj.innerHTML = data;
}

var table = null;
var a = null;
function showTheTable(id)
{
	if(table == null || a == null)
	{
		table = document.getElementById('expenses1');
		a = document.getElementById('expenseLink1');
	}
	table.style.display = 'none';
	a.className = 'tablenav';
	table = document.getElementById('expenses' + id);
	table.style.display = '';
	a = document.getElementById('expenseLink' + id);
	a.className = 'tablenav here';
}

function hideTheTable(link)
{
	if(table == null || a == null)
	{
		table = document.getElementById('expenses1');
		a = document.getElementById('expenseLink1');
	}
	table.style.display = 'none';
	a.className = 'tablenav';
	a = link;
	a.className = 'tablenav here';
}

var catText = "Select a category from the drop down menu. If you wish to create a new category, add a category by clicking on the link to the left. If you do not wish to include this transaction in your budget, choose &quot;Non Budget Transaction&quot;";
var amountText = "This is the amount of the transaction. Expenses are in parentheses, credits are in bold.";
var storeText = "This is the store or location of this transaction.";
var accountText = "This is the account to which the transaction belongs.";
var balanceText = "This is the balance of all your accounts after this transaction.";
var balance2Text = "This is the balance of this account after this transaction.";
{/literal}
</script>
<div style="display:none"><select id="exampleSelect"><option/>{html_options options=$categoryOptions}</select></div>
<table cellpadding="0" cellspacing="0" border="0" width="100%">
	<tr>
		<td colspan="3">
			<table cellspacing="0" cellpadding="2" class="list" width="100%">
				<tr>
					<th>
						Expense Information
					</th>
				</tr>
				<tr>
					<td>
{if count($expenses) > $iterations}
						<ul class="tablenav">
	{foreach from=$expenses name="expenses" key="expenseId" item="expense"}
		{if $smarty.foreach.expenses.iteration % $iterations == 1}
							<li><a id="expenseLink{$smarty.foreach.expenses.iteration}" class="tablenav {if $smarty.foreach.expenses.iteration == 1}here{/if}" href="#" onclick="showTheTable({$smarty.foreach.expenses.iteration}, this); return false;">{$smarty.foreach.expenses.iteration} - {$smarty.foreach.expenses.iteration+$iterations-1}</a></li>
		{/if}
	{/foreach}
							<li><a id="expenseLinkBudget" class="tablenav" href="#" onclick="hideTheTable(this); return false;">Budget</a></li>
						</ul>
{/if}
{foreach from=$expenses name="expenses" key="expenseId" item="expense"}
	{if $smarty.foreach.expenses.iteration % $iterations == 1}
		{if $smarty.foreach.expenses.iteration != 1}
						</table>
		{/if}
						<table  id='expenses{$smarty.foreach.expenses.iteration}' cellpadding=2 cellspacing=0
							style="clear: left;{if $smarty.foreach.expenses.iteration != 1}display: none{/if}" width="100%">
							<tr>
								<th>
									&nbsp;
								</th>
								<th>
									Category
								</th>
								<th>
									Amount
								</th>
								<th>
									Store
								</th>
								<th>
									Account
								</th>
								<th>
									Balance
								</th>
								<th>
									Date
								</th>
								<th>
									Delete
								</th>
							</tr>
		
	{/if}
							<tr id="expenseRow{$expenseId}" {if !empty($expense.color)}class="{$expense.color}"{else}class="white"{/if}>
								<td {if !empty($expense.color)}class="{$expense.color}"{else}class="white"{/if}>
									<a href="#" onclick="document.main_form.expenseId.value = {$expenseId}; submitForm('editExpense'); return false;">edit</a>
								</td>
								<td {helpup text="catText"}
									id="expenseCatCell{$expenseId}" {if !empty($expense.color)}class="{$expense.color}"{else}class="white"{/if}>
									<span style="display: none">{$expense.category}</span>
									<select name="category[{$expense.id}]" onchange="changeCategory({$expense.id}, this);" category_id="{$expense.c_id}">
										{*html_options options=$categoryOptions selected=$expense.c_id*}
									</select>
								</td>
								<td {helpup text="amountText"}
									{if !empty($expense.color)}class="{$expense.color}"{else}class="white"{/if}>{if $expense.credit == 0}({else}<b><em>{/if}{$expense.amount|currency}{if $expense.credit == 0}){else}</em></b>{/if}</td>
								<td {helpup text="storeText"}
									{if !empty($expense.color)}class="{$expense.color}"{else}class="white"{/if}>{$expense.store}</td>
								<td {helpup text="accountText"}
									{if !empty($expense.color)}class="{$expense.color}"{else}class="white"{/if}>{$expense.entered_by}</td>
								{if !isset($searchValues.entered_by)}
								<td {helpup text="balanceText"}
									{if !empty($expense.color)}class="{$expense.color}"{else}class="white"{/if}>{$expense.balance|round:2}</td>
								{else}
								<td {helpup text="balance2Text"}
									{if !empty($expense.color)}class="{$expense.color}"{else}class="white"{/if}>{$expense.thisbalance}</td>
								{/if}
								<td {if !empty($expense.color)}class="{$expense.color}"{else}class="white"{/if}>{$expense.date}</td>
								<td {if !empty($expense.color)}class="{$expense.color}"{else}class="white"{/if}>
									<a href="#" onclick="document.main_form.expenseId.value = {$expenseId}; submitForm('deleteExpense'); return false;" class="delete">X</a>
								</td>
							</tr>
{/foreach}
{if !empty($expenses)}
						</table>
{/if}
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<script>
{literal}
dojo.addOnLoad(function(){
example = document.getElementById('exampleSelect');
selects = document.getElementsByTagName('select');
for(s in selects)
{
	if(!selects[s].name || selects[s].name.substr(0,9) != 'category[')
		continue;
	for(o = 0; o < example.childNodes.length; o++)
	{
		newo = example.childNodes[o].cloneNode(true);
		if(selects[s].getAttribute('category_id') == newo.value)
			newo.selected = true;
		selects[s].appendChild(newo);
	}	
}
});
{/literal}
</script>
<input type="hidden" name="expenseId">
