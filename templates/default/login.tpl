{include file="header.tpl"}
<table width="100%" height="100%">
	<tr>
		<td valign="top" width="150" class="zbg-blue10 zbor-9 zc-blue1 zbc-blue1" >
			<br/>	
			<a href="{$SCRIPT_URL}/register">Register</a><br/>
		</td>
		<td width="700" valign="top">
			<div id="register" class="zbb-sspace" style="width: 700px;">
				<div style="float:left; width: 100px;">Email:</div><input type="text" name="email"><br/>
				<div style="float:left; width: 100px;">Password:</div><input type="password" name="password"><br/>
				<input type="submit" name="login" value="Login" onclick="submitForm('login');"><br/>
				- or -<br/>
				<input type="button" name="register" value="Register as a New User" onclick="location.href='{$SCRIPT_URL}/register'">
			</div>
		</td>
	</tr>
</table>

{include file="footer.tpl"}
