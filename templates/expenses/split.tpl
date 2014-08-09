{include file="header.tpl"}
<script type="text/javascript">
{literal}
function showMonthSpan(obj)
{
	if(obj.checked)
		document.getElementById('monthSpan').style.display = '';
	else
		document.getElementById('monthSpan').style.display = 'none';
}

function updateAmount()
{
	if(document.main_form.amount.value.length == 0)
		splitAmount = 0;
	else
		splitAmount = parseFloat(document.main_form.amount.value);
	origAmount = parseFloat(document.main_form.origAmount.value);
	document.main_form.newAmount.value = (origAmount - splitAmount).toFixed(2);
	document.getElementById('origAmountSpan').innerHTML = "$" + document.main_form.newAmount.value;	
}
{/literal}
</script>
<div class="zbg-blue10 zbc-blue1 zc-blue1 zbor-10 zt-tah zt-mxl" style="font-weight: bold; padding: 3px">
	<div style="text-align:center;">
		Edit Expense
	</div>
</div>
<table>
	<tr>
		<td valign="top" width="150" class="zbg-blue10 zbor-9 zc-blue1 zbc-blue1" >
			&nbsp;
		</td>
		<td width="80%">
			
			<table style="float:left; width: 345px;">
				<caption>The original expense</caption>
				<tr>
					<th>
						Category
					</th>
					<td>
						{assign var="catId" value=$expenseInfo.category_id}
						{$categories.$catId}
					</td>
				</tr>
				<tr>
					<th>
						Amount
					</th>
					<td>
						<span  id="origAmountSpan">${$expenseInfo.amount}</span>
						<input type="hidden" name="origAmount" value="{$expenseInfo.amount}">
						<input type="hidden" name="newAmount" value="{$expenseInfo.amount}">
					</td>
				</tr>
				<tr>
					<th>
						Store
					</th>
					<td>
						{$expenseInfo.store}
					</td>
				</tr>
				<tr>
					<th>
						Account:
					</th>
					<td>
						{assign var="acctId" value=$expenseInfo.entered_by}
						{$accounts.$acctId}
					</td>
				</tr>
				<tr>
					<th>
						Date
					</th>
					<td>
						{$expenseInfo.date|better_date_format:"%b %d, %Y"}
					</td>
				</tr>
			<table>
			<table style="float:left; width:345px">
				<caption>The split off expense</caption>
				<tr>
					<th>
						<label for='category_id'>Category:</label>
					</th>
					<td>
						<select name="category_id">
						{html_options options=$categories selected=$expenseInfo.category_id}
					</td>
				</tr>
				<tr>
					<th>
						Amount:
					</th>
					<td>
						<input type="text" name="amount" value="0" onKeyUp="updateAmount();">
					</td>
				</tr>
				<tr>
					<th>
						Store:
					</th>
					<td>
						{$expenseInfo.store}
					</td>
				</tr>
				<tr>
					<th>
						Account:
					</th>
					<td>
						{$accounts.$acctId}
					</td>
				</tr>
				<tr>
					<th>
						Date:
					</th>
					<td>
						{$expenseInfo.date|better_date_format:"%b %d, %Y"}
					</td>
				</tr>
				<tr>
					<th>
						{guicontrol_label guicontrol=$comment}
					</th>
					<td>
						{guicontrol guicontrol=$comment}
					</td>
				</tr>
			</table>
			<br/>
			<div>
				<input type="button" name="continue" onclick="submitForm('save'); return false;" value="Save"><br/>
				<input type="button" name="cancel" onclick="document.location.href='{$zoneUrl}/list'" value="Cancel">
			</div>
		</td>
	</tr>
</table>

{include file="footer.tpl"}
