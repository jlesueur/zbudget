{assign var="colors" value=$strings.colors}
<div style="font-size: {cycle values="14pt,12pt,10pt"}; text-align: center; font-weight: bold">Budget and Expenses for {$strings.months.$month} {$year}</div>
<verticalbarchart grouping="side" style="border: 0">
	<chartcat name="budgeted" text="Budgeted Funds" color="{$strings.colors.blue.chart}"/>
	<chartcat name="expenses" text="Expenses" color="{$strings.colors.red.chart}"/>
	{foreach from=$categories key="id" item="cat"}
		{assign var="color" value=$cat.color}
		{assign var="color" value=$colors.$color}
		<chartgroup name="{$cat.name}" text="{$cat.name}" color="{$color.chart}"/>
		<chartdata category="expenses" group="{$cat.name}" 
					value="{$cat.spent}" url="javascript: popup('expenses', 'taxes')"/>
		<chartdata category="budgeted" group="{$cat.name}" 
					value="{$cat.amount}" url="javascript: popup('budgeted', 'taxes')"/>
	{/foreach}
	<div style="border: 0;text-align:center">
		<div width="750" style="border: 0">
			<chartplot height="350" depthvector="0,0"/>
		</div>
	</div>
	<div style="height: 10"/>
	<div style="border: 0;text-align:center">
		<chartlegend/>
	</div>
	<div style="height: 10"/>
	<div style="font-size: 12pt; text-align: center; font-weight: bold">Printed {$smarty.now|date_format:"%A, %B %e, %Y"}</div>
</verticalbarchart>