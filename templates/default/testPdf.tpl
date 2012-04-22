{assign var="colors" value=$strings.colors}
<horizontalbarchart grouping="simple" style="border: 0" depth="20">
	<chartgroup name="budgeted" text="Budgeted Funds" />
	<chartgroup name="expenses" text="Expenses" />
	{foreach from=$categories key="id" item="cat"}
		{assign var="color" value=$cat.color}
		{assign var="color" value=$colors.$color}
		<chartcat name="{$cat.name}" text="{$cat.name}" color="{$color.chart}"/>
		<chartdata category="{$cat.name}" group="budgeted" 
					value="{$cat.amount}" url="javascript: popup('budgeted', 'taxes')"/>
		<chartdata category="{$cat.name}" group="expenses" 
					value="{$cat.spent}" url="javascript: popup('expenses', 'taxes')"/>
	{/foreach}
	<div style="border: 0;text-align:center">
		<div width="600" style="border: 0">
			<chartplot height="300" depthvector="35,15"/>
		</div>
	</div>
	<div style="border: 0;text-align:center; height: 30">
		2 items
		{*
			<chartstring text="Total Population = %n" />
		*}
	</div>
	<div style="border: 0;text-align:center">
		<chartlegend/>
	</div>
	<div style="height: 10"/>
	<div style="font-size: 12pt; text-align: center; font-weight: bold">Printed {$smarty.now|date_format:"%A, %B %e, %Y"}</div>
</horizontalbarchart>

<verticalbarchart grouping="side" style="border: 0">
	<chartcat name="expenses" text="Expenses" color="{$strings.colors.red.chart}"/>
	<chartcat name="budgeted" text="Budgeted Funds" color="{$strings.colors.purple.chart}"/>
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
		<div width="580" style="border: 0">
			<chartplot height="300" depthvector="0,0"/>
		</div>
	</div>
	<div style="height: 10"/>
	<div style="border: 0;text-align:center; height: 30">
		<chartstring text="Total Population = %n" />
	</div>
	<div style="border: 0;text-align:center">
		<chartlegend/>
	</div>
	<div style="height: 10"/>
	<div style="font-size: 12pt; text-align: center; font-weight: bold">Printed {$smarty.now|date_format:"%A, %B %e, %Y"}</div>
</verticalbarchart>
<piechart style="border: 0" depth="20">
	{foreach from=$categories key="id" item="cat"}
		{assign var="color" value=$cat.color}
		{assign var="color" value=$colors.$color}
		<chartdata text="{$cat.name}" value="{$cat.amount}" color="{$color.chart}" url="javascript: viewSummary('{$cat.name}', 'budgeted')"/>
	{/foreach}
	<div style="border: 0; text-align:center">
		<div width="400" style="border: 0;">
			<chartplot height="250"/>
		</div>
	</div>
	<div style="border: 0;text-align:center; height: 30">
		<chartstring text="Total Population = %n" />
	</div>
	<div style="border: 0;text-align:center">
		<chartlegend/>
	</div>
</piechart>