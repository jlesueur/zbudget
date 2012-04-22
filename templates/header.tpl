<head>
	<BASE href="{$SCRIPT_URL}">
	<script>
	function submitForm(action)
	{literal}
	{
		if(action == document.main_form)
		{
			action.submit();
			return;
		}
		document.main_form.actionField.value = action;
		document.main_form.submit();
	}
	{/literal}
	</script>
	<style>
{foreach from=$strings.colors key="class" item="color"}
	.{$class}{literal}
	{{/literal}
	
		background-color: {$color.background};
		color: {$color.text};
	{literal}}{/literal}
	.{$class} a
	{literal}{{/literal}
		color: {$color.text};
	{literal}}{/literal}
	.{$class} a.delete
	{literal}{{/literal}
		color: {$color.text};
	{literal}}{/literal}
	td.{$class}, tr.{$class}, .{$class} td
	{literal}{{/literal}
		border: thin solid {$color.text};
	{literal}}{/literal}
{/foreach}
	{literal}
	td.white, tr.white, .white td
	{
		border: thin solid #000000;
	}
	
	#container
	{
		width: 500px;
		text-align: center;
		margin-left: 100px;
		background-color:#EEEEEE
	}

	table
	{
		border-collapse: collapse;
	}

	.zbg-purple1
	{
		background-color: #AA55FF;
	}

	.zbg-purple2
	{
		background-color: #6655AA;
	}

	.zc-purple1
	{
		color: #770077;
	}

	.zc-purple2
	{
		background-color: #6655AA;
	}

	a
	{
		color: #3F4C6B;
	}
	{/literal}
	{literal}
	#doc
	{
		width:100%;
		/*width:900px;*/
		min-width:500px;
		text-align:left;
	}
	
	body
	{
		padding: 0px;
		spacing: 0px;
		margin: 0px;
	}
	
	.error
	{
		color: #FF3333;
	}
	
	.tablenav {
		position:relative;
		float:left;
		padding:0 0 0 0;
		margin:0;
		list-style:none;
		line-height:1em;
	}

	.tablenav LI {
		float:left;
		margin:0;
		padding:0;
	}

	.tablenav A.tablenav {
		display:block;
		color:#3F4C6B;
		text-decoration:none;
		font-weight:bold;
		background:#F1FDFF;
		margin:0;
		padding:0.25em 1em;
		border-left:1px solid #5d96be;
		border-top:1px solid #5d96be;
		border-right:1px solid #5d96be;
	}

	.tablenav A:hover,
	.tablenav A:active,
	.tablenav A.here:link,
	.tablenav A.here:visited {
		background:#5d96be;
	}

	.tablenav A.here:link,
	.tablenav A.here:visited {
		position:relative;
		z-index:102;
	}
	{/literal}
	</style>
	{zoop_file_css files="zoop_styles.css"}
	{zoop_file_js files="sorttable.js"}
	{zoop_file_js files="ajax/prototype.js"}
	{zoop_file_js files="pValidate/validation.js"}
	{zoop_file_js files="ajax/dojo/dojo.js"}
	{popup_init src="$SCRIPT_URL/zoopfile/gui/overlib.js"}
{if isset($title)}
	<title>{$title}</title>
{else}
	<title>Budget</title>
{/if}
</head>
<body>
<form id="main_form" name="main_form" method="POST" action="{$VIRTUAL_URL}" enctype="multipart/form-data">
<div id="doc">
{if isLoggedIn()}<div style="float:right; margin-top:10px; margin-right: 6px;"><a href="{$SCRIPT_URL}/logout">Sign Off</a></div>{/if}
<div style="float:right; margin-top:10px; margin-right: 6px;"><a href="mailto:john.lesueur@gmail.com">Contact John</a></div>
<div style="float:right; margin-top:10px; margin-right: 6px;"><a href="#" onclick="return false;" {popup snapx=10 snapy=10 vauto=true hauto=true text="If you have questions, hover over the text you are having trouble with. If your question is not answered, click on Contact John to email your question to John."}>Help</a></div>
{if isset($title)}
	<h1 class="zbg-blue10 zbc-blue1 zc-blue1 zbor-10 zt-tah zt-mxl zbb-sspace" style="margin: 0px; text-align: center;">
		{$title}
	</h1>
{/if}
