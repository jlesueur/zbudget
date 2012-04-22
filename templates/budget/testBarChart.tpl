<div style="font-size: {cycle values="14pt,12pt,10pt"}; text-align: center; font-weight: bold">Budget and Expenses for Jan 2008</div>
<verticalbarchart grouping="side" style="border: 0">
	<chartcat name="budgeted" text="Budgeted Funds" color="#0000FF"/>
	<chartcat name="expenses" text="Expenses" color="#FF0000"/>
	{foreach from=$data key="id" item="cat"}
		<chartgroup name="{$cat.name}" text="{$cat.name}" color="{$cat.color}"/>
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