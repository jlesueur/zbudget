{include file="header.tpl"}
<table width="100%" height="100%">
	<tr>
		<td valign="top" width="150" class="zbg-blue10 zbor-9 zc-blue1 zbc-blue1" >
			<br/>	
			Registration Actions<br/>
			<a href="">Login</a><br/>
		</td>
		<td width="700" valign="top">
			<div id="register" class="zbb-sspace" style="width: 700px;">
				<p>
				Welcome to zBudget, a home budgeting system. To get started, enter your email address, which will be used to log in to the system.
				<br/>
				{guicontrol_label guicontrol=$email} {guicontrol guicontrol=$email}{if isset($error) && $error == 'email'}<span class="error">An account already exists for this email address. <a href="{$zoneUrl}/emailPassword">Click Here</a> to have zBudget reset your password</span>{/if}
				</p>
				<p>
				Next, we'll get a password. Keep in mind that there will be a record of your financial transactions and account numbers, which may be sensitive information. Please 
				enter your password twice to confirm that we get it right.
				<br/>
				Password: {guicontrol guicontrol=$password}
				<br/>
				Password: {guicontrol guicontrol=$password2}{if isset($error) && $error == 'password'}<span class="error">Passwords did not match</span>{/if}
				</p>
				<p>
				Currently, zBudget is only open to invited users. Invited users have been provided with a secret code. 
				<br/>
				{guicontrol_label guicontrol=$secretcode} {guicontrol guicontrol=$secretcode}{if isset($error) && $error == 'secretcode'}<span class="error">Please enter the correct secret code.</span>{/if}
				</p>
				<input type="button" name="register" value="Register" onclick="submitForm('register');">
			</div>
		</td>
	</tr>
</table>

{include file="footer.tpl"}
