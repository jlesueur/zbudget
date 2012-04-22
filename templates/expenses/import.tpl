{include file='header.tpl'}
<div class="zbg-blue10 zbc-blue1 zc-blue1 zbor-10 zt-tah zt-mxl" style="font-weight: bold; padding: 3px">
	<div style="text-align:center;">
		Import Expense File
	</div>
</div>
<table>
	<tr>
		<td valign="top" width="20%" class="zbg-blue10 zbor-9 zc-blue1 zbc-blue1" >
			Expense Actions<br>
			<a href="{$zoneUrl}/list" class="zt-m">View Expenses</a><br>
		</td>
		<td width="80%">
			<p>
				zBudget can import transactions from a download from your bank. Follow the instructions from your bank to download your account activity in a format
				compatible with MS Money, Quicken, or QuickBooks. If you need help downloading your transactions, contact the customer service at your bank.<br/>
				<span style="font-size: small;">*Duplicate transactions will be detected, so you don't have to remember which transactions you have uploaded. </span>
			</p>
			<p>
				To begin the import, choose the file you downloaded from the bank, then click upload. After the upload, you will be presented with a list of the 
				transactions imported, so that you can categorize transactions and correct any mistakes.
			</p>
			<table>
				{*
				<tr>
					<td>
						<input type="radio" name="format" value="zionsbank" checked>ZionsBank (no account required)<br/>
						<input type="radio" name="format" value="wellsfargo">WellFargo (no account required)<br/>
						<input type="radio" name="format" value="mbna">MBNA (please choose an account)<br/>
						<input type="radio" name="format" value="capitalone">Capital One (please choose an account)<br/>
						<input type="radio" name="format" value="ofx">MS Money, Quicken, QuickBooks Format(any bank)
					</td>
				</tr>
				<tr>
					<td>
						
						Please choose a default account for this upload:
						<select name="account_id">
							{html_options options=$accounts}
						</select>
						<br/>
					</td>
				</tr>
				*}
				<tr>
					<td>
						<input type="hidden" name="format" value="ofx">
						Filename: <input type="file" name="importFile">
						<input type="button" value="Upload" onclick="submitForm('next'); return false;"><input type="button" value="Cancel" onclick="submitForm('cancel'); return false;">
					</td>
				</tr>
			</table>
			<br/>
			<br/>
		</td>
	</tr>
</table>
{include file='footer.tpl'}
