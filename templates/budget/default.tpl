{include file="header.tpl"}
{include file="choosePeriod.tpl" baseUrl=$zoneUrl}
{include file="budgetList.tpl"}
<input type="button" name="AddCat" value="Add Category" onClick="submitForm('addCat'); return false;">
<input type="button" name="ViewExp" value="View Expenses" onclick="submitForm('viewExpenses'); return false;">
{include file="footer.tpl"}