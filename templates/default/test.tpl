<form name="main_form" method="POST" action="{$VIRTUAL_URL}">
{zoop_file_js files="openwysiwyg/wysiwyg.js"}
{zoop_file_js files="prototype.js" component="projax"}
{zoop_file_js files="scriptaculous.js" component="projax"}
Editor: {guicontrol name="test" type="openwysiwyg"}
{guicontrol name="text" type="autocomplete" url="$zoneUrl/testAutoComplete"}


<input type="submit" name="submit" value="submit">
</form>