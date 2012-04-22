{include file="header.tpl"}
<div class="zbg-blue10 zbc-blue1 zc-blue1 zbor-10 zt-tah zt-mxl" style="font-weight: bold; padding: 3px">
		<div style="text-align:center;">
			Edit a Budget Category for {$month} / {$year}
		</div>
</div>
<table>
	<tr>
		<td valign="top" width="150" class="zbg-blue10 zbor-9 zc-blue1 zbc-blue1" >
			<a href="{$SCRIPT_URL}/expenses/{$year}/{$month}/list" class="zt-m">View Expenses</a><br>
		</td>
		<td width="80%">
			<div>
				<div style="float: left; width: 100px;">
					Name:
				</div>
				<div>
					{guicontrol guicontrol=$name}
				</div>
			</div>
			<div>
				<div style="float: left; width: 100px;">
					Amount:
				</div>
				<div>
					{guicontrol guicontrol=$amount} {guicontrol guicontrol=$fund}
					{*{guicontrol guicontrol=$income}*}
				</div>
			</div>
			<div>
				<div style="float: left; width: 100px;">
					Color:
				</div>
				<div>
					
					{*{guicontrol guicontrol=$color}*}
					<select name="color">
					{foreach from=$colors key="value" item="name"}
						<option class="{$value}" value="{$value}" {if $color->getValue() == $value}selected="selected"{/if}>{$name}</option>
					{/foreach}
					</select>
				</div>
			</div>
			<div>
				<div style="float: left; width: 100px;">
					Description:
				</div>
				<div>
					{guicontrol guicontrol=$description}
				</div>
			</div>
			<div>
				<div style="float: left; width: 100px;">
					Comments:
				</div>
				<div>
					{guicontrol name="comments" type="textarea"}
				</div>
			</div>
			<div>
				<input type="button" name="save" value="Save" onclick="submitForm('save'); return false;">
				<input type="button" name="cancel" value="Cancel" onclick="document.location.href='{$SCRIPT_URL}/expenses/{$year}/{$month}/list';">
			</div>
		</td>
	</tr>
</table>


{include file="footer.tpl"}