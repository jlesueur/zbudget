{if $param.type == 'options'}
{if isset($param.displayName)}
<label for="{$name}">{$param.displayName}</label>
{/if}
<select name='{$name}'>
	{if !isset($param.hideAny)}
		<option value="">Any</option>
	{/if}
	{if isset($searchValues.$name)}
		{html_options options=$param.options selected=$searchValues.$name}
	{else}
		{html_options options=$param.options}
	{/if}
</select>
{/if}