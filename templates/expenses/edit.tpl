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
			<table>
				<tr>
					<td>
						<label for='category_id'>Category:</label>
					</td>
					<td>
						<select name="category_id">
						{html_options options=$categories selected=$expenseInfo.category_id}
					</td>
				</tr>
				<tr>
					<td>
						{guicontrol_label guicontrol=$amount}
					</td>
					<td>
						{guicontrol guicontrol=$amount}
						{guicontrol guicontrol=$credit}
					</td>
				</tr>
				<tr>
					<td>
						{guicontrol_label guicontrol=$store}
					</td>
					<td>
						{guicontrol guicontrol=$store}
					</td>
				</tr>
				<tr>
					<td>
						{guicontrol_label guicontrol=$enteredBy}
					</td>
					<td>
						{guicontrol guicontrol=$enteredBy}
					</td>
				</tr>
				<tr>
					<td>
						{guicontrol_label guicontrol=$comment}
					</td>
					<td>
						{guicontrol guicontrol=$comment}
					</td>
				</tr>
				<tr>
					<td>
						{guicontrol_label guicontrol=$date}
					</td>
					<td>
						{guicontrol guicontrol=$date}
					</td>
				</tr>
				<tr>
					<td>
						<a target="_blank" href="http://www.google.com/search?q=define%3Aamortize&ie=utf-8&oe=utf-8&rls=org.mozilla:en-US:official&client=firefox-a">Amortize</a> this expense
					</td>
					<td>
						<input type="checkbox" value="1" name="repeat" onclick="showMonthSpan(this);"{if $expenseInfo.span_months > 1} checked{/if}>
						<span id="monthSpan"{if $expenseInfo.span_months <= 1} style='display: none'{/if}>
							over <input name="span_months" type="text" class="validate-digits" size="2" value="{$expenseInfo.span_months}"> Months
						</span>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<a href="#" onclick="submitForm('split'); return false;">Click here to split this expense into multiple categories</a>
					</td>
				</tr>
			</table>
			<br/>
			<div>
				<input type="button" name="continue" onclick="submitForm('save'); return false;" value="Save"><br/>
				<input type="button" name="continue" onclick="submitForm('continue'); return false;" value="Save and Continue Data Entry"><br/>
				<input type="button" name="cancel" onclick="document.location.href='{$zoneUrl}/list'" value="Cancel">
			</div>
		</td>
	</tr>
</table>

{include file="footer.tpl"}