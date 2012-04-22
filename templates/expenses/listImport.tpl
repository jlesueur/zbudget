{include file="header.tpl"}

<table width="100%">
	<tr>
		<td valign="top" width="20%" class="zbg-blue10 zbor-9 zc-blue1 zbc-blue1" >
			
		</td>
		<td width="80%">
			<div id="expenseList" class="zbb-sspace">
				These are the expenses that were imported from your uploaded file. You can review and categorize them here, or you can 
				<a href="#" onclick="submitForm('done'); return false;">skip this step&gt;&gt;&gt;</a> and go back to viewing all your expenses for this month.<br/>
				<input type="button" onclick="submitForm('done'); return false;" name="done" value="Done&gt;">
				{include file="expenseList.tpl"}
				<input type="button" onclick="submitForm('done'); return false;" name="done" value="Done&gt;">
				{*{forms2 form=$form empty_error="No Expenses for this Month" sort_type="forms"}*}
			</div>
		</td>
	</tr>
</table>

{include file="footer.tpl"}
