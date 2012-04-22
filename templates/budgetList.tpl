<div class="zt-m" style="font-weight: bold; text-align: center">
	
</div>
<script>
{literal}
var catSpent = "How much you spent this month in this category.";
var catBudgeted = "How much you budgeted to spend this month in this category.";
var catLeft = "How much you have left in this category.(a negative indicates you spent more than was in your budget)";
var budgetSummary = "<ol><li>How much you spent this month in budgeted categories</li><li>The total amount you budgeted this month</li><li>How much is left in your budget for this month.(a negative indicates you spent more than was in your budget)</li></ol>";
{/literal}
</script>
<table cellpadding=0 cellspacing=0 border=0>
	<tr>
		<td colspan="3">
			<table cellspacing="0" cellpadding="2" class="list">
				<tr>
					<th>
						Budget Information
					</th>
				</tr>
				<tr>
					<td>
						<table  id='budget' cellpadding=2 cellspacing=0>
							<tr>	
								<th>
									Category
								</th>
								<th>
									Spent
								</th>
								<th>
									Allocated
								</th>
								<th>
									Left
								</th>
								<th>
									Delete
								</th>
							</tr>
{foreach from=$categories key="catId" item="category"}
							<tr{* class="{$category.color}"*} style="border: solid black 1px;">
								<td align="right">
									<a href="#" onclick="document.main_form.catId.value = {$catId}; submitForm('editCat'); return false;">{$category.name}</a>
								</td>
								<td align="right"
									{helpup text="catSpent"}>
									{$category.total|default:"0.00"|currency}
								</td>
								<td align="right"
									{popup delay=500 snapx=10 snapy=10 vauto=true hauto=true text="catBudgeted"}>
									{$category.amount|currency}
								</td>
								<td align="right"
									{popup delay=500 snapx=10 snapy=10 vauto=true hauto=true text="catLeft"}>
									{if $category.left < 0}<span style="color: red">{$category.left|default:$category.amount|currency}</span>
									{else}
										{$category.left|default:$category.amount|currency}
									{/if}
								</td>
								<td>
									<a href="{$SCRIPT_URL}/budget/deleteCat/{$catId}/{$year}/{$month}" class="delete">X</a>
								</td>
							</tr>
{/foreach}
							<tr>
								<td align="right" {popup snapx=10 snapy=10 vauto=true hauto=true text="budgetSummary"}>
									<em>
									Budget Totals:
									</em>
								</td>
								<td align="right" {popup snapx=10 snapy=10 vauto=true hauto=true text="How much you spent this month in budgeted categories"}>
									{$total.total|currency}
								</td>
								<td align="right" {popup snapx=10 snapy=10 vauto=true hauto=true text="The total amount you budgeted this month"}>
									{$total.amount|currency}
								</td>
								<td align="right" {popup snapx=10 snapy=10 vauto=true hauto=true text="How much is left in your budget for this month.(a negative indicates you spent more than was in your budget)"}>
									{$total.left|currency}
								</td>
								<td align="right">
									&nbsp;
								</td>
							</tr>
							<tr>
								<td align="right" {popup snapx=10 snapy=10 vauto=true hauto=true text="How much you spent this month outside your budget. Any transactions categorized as Non-Budget Transactions are counted here."}>
									<em>
										Non Budget Totals:
									</em>
								</td>
								<td align="right" {popup snapx=10 snapy=10 vauto=true hauto=true text="How much you spent this month outside your budget. Any transactions categorized as Non-Budget Transactions are counted here."}>
									{if $unbudget.total > 0}<b><em>{$unbudget.total|currency}</b></em>
									{else}({$unbudget.total*-1|currency})
									{/if}
								</td>
								<td align="right">
									{*{$unbudget.amount|currency}*}
								</td>
								<td align="right">
									{*{$unbudget.left|currency}*}
								</td>
								<td align="right">
									&nbsp;
								</td>
							</tr>
							<tr>
								<td align="right" {popup snapx=10 snapy=10 vauto=true hauto=true text="This is your total cash flow for this month. If it is positive, you made more than you spent. If it is negative, you spent more than you made."}>
									<em>
									Total Flow:
									</em>
								</td>
								<td align="right"
									{popup snapx=10 snapy=10 vauto=true hauto=true text="This is your total cash flow for this month. If it is positive, you made more than you spent. If it is negative, you spent more than you made."}>
									{if $flow > 0}<b><em>{$flow|currency}</b></em>
									{else}({$flow*-1|currency})
									{/if}
								</td>
							</tr>
							<tr>
								<td>
									<em>Balances:</em>
								</td>
							</tr>
							{foreach from=$accountTotals item="account"}
							<tr>
								<td>
									{$account.account}
								</td>
								<td align="right">
									{$account.balance}
								</td>
							</tr>
							{/foreach}
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<input type="hidden" name="catId">
