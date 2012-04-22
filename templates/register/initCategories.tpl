{include file="header.tpl"}
<table width="100%" height="100%">
	<tr>
		<td valign="top" width="150" class="zbg-blue10 zbor-9 zc-blue1 zbc-blue1" >
			<br/>	
		</td>
		<td width="700" valign="top">
			<div id="register" class="zbb-sspace" style="width: 700px;">
				<p>
					You can create your budget here, by creating categories, and assigning amounts to them. Each budget category will have a color associated with it. 
				</p>
				<p>
					Don't worry about making your budget perfect now. You will be able to make changes later. Each month you will be able to change the amounts for 
					categories, or even add new categories if you wish. After two months of budgeting you may have a better idea of how much to allocate in each category.
				</p>
				<p>
					Some standard categories you may want to have are:
					<ul>
						<li>
							Rent/Mortgage
						</li>
						<li>
							Food
						</li>
						<li>
							Utilities (you may even want to have more specific categories like phone, gas, electricity, etc.)
						</li>
						<li>
							Clothing
						</li>
					</ul>
				</p>
						
<script>
{literal}
dojo.require("dojo.io.IframeIO");
function addCategory()
{
	thing = dojo.io.bind({
			url:
			{/literal}
				"{$zoneUrl}/addCategory",
			{literal}
			method: 'post',
			//mimetype: 'text/html',
			formNode: document.main_form,
			handler: function(type, data, e){updateCategoryList(data);}
			//transport: 'IframeTransport'
			});
}

function updateCategoryList(data)
{
	accountList = document.getElementById('categoryList');
	accountList.innerHTML = data;
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
				<div id="categoryList">
{include file="register/displayCategories.tpl"}
				</div>
				<input type="button" name="register" value="Done" onclick="submitForm('done');">
			</div>
		</td>
	</tr>
</table>

{include file="footer.tpl"}
