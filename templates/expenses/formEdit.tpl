{include file="header.tpl"}
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
			{forms2 form=$form onclick="submitForm('save'); return false;"}
			<div>
				<input type="button" name="continue" onclick="submitForm('continue'); return false;" value="Save and Continue"><br/>
				<input type="button" name="cancel" onclick="submitForm('cancel'); return false;" value="Cancel">
			</div>
		</td>
	</tr>
</table>

{include file="footer.tpl"}