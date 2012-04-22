<div style="font-size: 14pt; text-align: center; font-weight: bold">Jan 2008</div>
<table>
	<tr>
		<td>
			<div style="font-size: 12pt; text-align: center; font-weight: bold">Budget</div>
			<piechart style="border: 0" depth="20">
				{foreach from=$data key="id" item="cat"}
					<chartdata text="{$cat.name}" value="{$cat.amount}" color="{$cat.color}" url="javascript: viewSummary('{$cat.name}', 'budgeted')"/>
				{/foreach}
				<div style="border: 0; text-align:center">
					<div width="250" style="border: 0;">
						<chartplot height="195"/>
					</div>
				</div>
				<div style="border: 0;text-align:center; height: 30">
					<chartstring text="Total Budget = %n" />
				</div>
				<div style="border: 0;text-align:center">
					<chartlegend/>
				</div>
			</piechart>
		</td>
		<td>
			<div style="font-size: 12pt; text-align: center; font-weight: bold">Budget</div>
			<piechart style="border: 0" depth="20">
				{foreach from=$data key="id" item="cat"}
					<chartdata text="{$cat.name}" value="{$cat.spent}" color="{$cat.color}" url="javascript: viewSummary('{$cat.name}', 'budgeted')"/>
				{/foreach}
				<div style="border: 0; text-align:center">
					<div width="250" style="border: 0;">
						<chartplot height="195"/>
					</div>
				</div>
				<div style="border: 0;text-align:center; height: 30">
					<chartstring text="Total Budget = %n" />
				</div>
				<div style="border: 0;text-align:center">
					<chartlegend/>
				</div>
			</piechart>
		</td>
	</tr>
</table>