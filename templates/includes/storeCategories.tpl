<script>
{literal}
function addStoreCategory()
{
	submitForm('add');
}

function moveUp(id, position)
{
	document.main_form.storeCategoryId.value = id;
	posField = document.main_form.newPosition;//.getElementById('positionField[' + id + ']');
	posField.value = position-1;
	submitForm('moveUp');
}

function moveDown(id, position)
{
	document.main_form.storeCategoryId.value = id;
	posField = document.main_form.newPosition;//document.getElementById('positionField[' + id + ']');
	posField.value = position+1;
	submitForm('moveDown');
}

function changeCategory(id, obj)
{
	//skip the ajax for now...
	document.main_form.storeCategoryId.value = id;
	submitForm('changeCategory');
}

function changeStore(id, obj)
{
	document.main_form.storeCategoryId.value=id;
	submitForm('changeStore');
}
{/literal}
</script>
<input type="hidden" name="storeCategoryId">
<input type="hidden" name="newPosition">
<table>
	<tr>
		<th>
			Store with names like:
		</th>
		<th>
			Category
		</th>
		<th>
			Order
		</th>
	</tr>
	<tr>
		<td>
			<input type="text" name="store[new]" value="">
		</td>
		<td>
			<select name="storecategory[new]">
				<option/>
				{html_options options=$categoryOptions}
			</select>
		</td>
		<td>
			<input type="button" onclick="addStoreCategory();" value="ADD">
		</td>
	</tr>
{counter name='order' start=0 print=false}
{foreach name="storeCategories" from=$storeCategories key="id" item="category"}
	<tr id="storeRow{$id}" {if !empty($category.color)}class="{$category.color}"{else}class="white"{/if}>
		<td>
			<input type="text" name="store[{$id}]" value="{$category.store}" onkeydown="this.nextSibling.style.display='';"><a style="display:none" href="#" onclick="changeStore({$id}, this); return false;">save</a>
		</td>
		<td {helpup text="Select a category from the drop down menu. If you wish to create a new category, add a category by clicking on the link to the left."}
			id="storeCell{$id}" {if !empty($category.color)}class="{$category.color}"{else}class="white"{/if}>
			<span style="display: none">{$category.category_id}</span>
			<select name="storecategory[{$id}]" onchange="changeCategory({$id}, this);">
				<option/>
				{html_options options=$categoryOptions selected=$category.category_id}
			</select>
		</td>
		<td>
			{counter name='order' assign="position"}
			<input type="hidden" id="positionField[{$id}]" name="position[{$id}]" value="{$position}">
			{if $position != 1}
			<a href="#" onclick="moveUp({$id}, {$position});return false;">move up</a>
			{else}
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			{/if}
			{if !$smarty.foreach.storeCategories.last}
			<a href="#" onclick="moveDown({$id}, {$position});return false;">move down</a>
			{/if}
		</td>
	</tr>
{/foreach}
</table>
