{assign var="colors" value=$strings.colors}
<div style="font-size: 14pt; text-align: center; font-weight: bold">{$strings.months.$startMonth} {$startYear} - {$strings.months.$endMonth} {$endYear}</div>
<table>
	<tr>
		<td>
			<div style="font-size: 12pt; text-align: center; font-weight: bold">Cash Flow</div>
			<linechart style="border: 0" depth="20">
				{foreach from=$accountNames key="accountId" item="account"}
					<chartcat name="account{$account.name}" text="{$account.name}" color="{$account.color}"/>
				{/foreach}
				{foreach from=$accounts key="date" item="day"}
					<chartgroup name="date{$date}" text="{$date|better_date_format:"%m/%e/%y"}" />
					{assign var="total" value=0}
					{foreach from=$day item="balance"}
						<chartdata category="account{$balance.name}" group="date{$date}" value="{$balance.balance}" />
						{assign var="total" value=$total+$balance.balance}
					{/foreach}
						<chartdata category="accountTotal" group="date{$date}" value="{$total}" />
				{/foreach}
				<div style="border: 0; text-align:center">
					<div width="780" style="border: 0;">
						<chartplot width="780" height="400"/>
					</div>
				</div>
				<div style="border: 0;text-align:center">
					<chartlegend/>
				</div>
			</linechart>
		</td>
	</tr>
</table>