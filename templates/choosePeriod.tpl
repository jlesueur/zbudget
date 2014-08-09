	<h1 class="zbg-blue10 zbc-blue1 zc-blue1 zbor-10 zt-tah zt-mxl zbb-sspace" style="margin: 0px; text-align: center;">
		<a class="zc-blue7" href="{$prevPeriod}">&lt;</a>
			<select id="chooseMonth" name="chooseMonth" onchange="gotoPeriod(this);" style="font-size:1.1em">
				{html_options options=$strings.months selected=$month}
			</select>
			<select id="chooseYear" name=chooseYear" onchange="gotoPeriod(this);" style="font-size:1.1em">
				<option>{$year-1}</option>
				<option selected>{$year}</option>
				<option>{$year+1}</option>
			</select>
		<a class="zc-blue7" href="{$nextPeriod}">&gt;</a>
	</h1>
<script>
{literal}
function gotoPeriod() {
	var year = document.getElementById('chooseYear').value;
	var month = document.getElementById('chooseMonth').value;
	console.log(month);
	console.log("{/literal}{$SCRIPT_URL}/expenses/"+year+ "/" + month + "/list");{literal}
	document.location="{/literal}{$SCRIPT_URL}/expenses/" + year + "/" + month + "/list{foreach from=$searchValues key="param" item="value"}/{$param}/{$value}{/foreach}";
{literal}
}
{/literal}
</script>
