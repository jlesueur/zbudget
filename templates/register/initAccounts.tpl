{include file="header.tpl"}
<table width="100%" height="100%">
	<tr>
		<td valign="top" width="150" class="zbg-blue10 zbor-9 zc-blue1 zbc-blue1" >
			<br/>	
		</td>
		<td width="700" valign="top">
			<div id="register" class="zbb-sspace" style="width: 700px;">
				<p>
					If you don't want to sync zBudget with your bank, but are going to manually enter transactions, you can <a href="#" onclick="submitForm('skip'); return false;">skip this step&gt;&gt;</a><br/>
					You will be able to change your mind later, if you wish.
				</p>
				<p>
					zBudget can work with downloads of your financial activity from your bank. Follow the instructions from your bank to download your account activity in a format
					compatible with MS Money, Quicken, or QuickBooks.
					{*For instructions on downloading files from Zionsbank, go to <a href="http://johnlesueur.blogspot.com/2008/06/zbudget-importing-instructions.html">these instructions.</a>*}
				</p>
				<p>
					<ol>
						<li>
							Please upload a file from your bank, and zBudget will set up your accounts and balances.
							<input type="hidden" name="format" value="ofx">
							<input type="file" name="importFile"><input type="button" name="import" value="Import" onclick="submitForm('upload');">
						</li>
						<li>
							You can confirm the accounts and balances after the upload, and manually add any accounts that are missing.
						</li>
					</ol>
				</p>
<script>
{literal}
dojo.require("dojo.io.IframeIO");
function addAccount()
{
	thing = dojo.io.bind({
			url:
			{/literal}
				"{$zoneUrl}/addAccount",
			{literal}
			method: 'post',
			mimetype: 'text/html',
			formNode: document.main_form,
			handler: function(type, data, e){updateAccountList(data);},
			transport: 'IframeTransport'
			});
}

function updateAccountList(data)
{
	accountList = document.getElementById('accountList');
	accountList.innerHTML = data.body.innerHTML;
}

function printObj(obj)
{
	text = "";
	count = 0;
	for(i in obj)
	{
		text += i + "=>" + obj[i] + "\r\n";
		if((++count % 20) == 0)
		{
			alert(text);
			text = "";
		}
	}
	if(text.length)
		alert(text);
}
{/literal}
</script>
				<div id="accountList">
{include file="includes/displayAccounts.tpl"}
				</div>
				<input type="button" name="register" value="Done" onclick="submitForm('done');">
			</div>
		</td>
	</tr>
</table>

{include file="footer.tpl"}
