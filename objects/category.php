<?php
class Category
{
	function getEndDay($month, $year)
	{
		if(($month < 8 && $month % 2 != 0) || ($month >= 8 && $month % 2 == 0))
			$endday = 31;
		else
			$endday = 30;
		if($month == 2)
			if($year % 4 == 0 && ($year % 100 != 0 || $year % 400 == 0))
				$endday = 29;
			else
				$endday = 28;
		return $endday;
	}

	function getBudgetList($userId, $month, $year)
	{
		$endday = Category::getEndDay($month, $year);
		$prevmonth = $month-1;
		$prevyear = $year;
		if($prevmonth == 0)
		{
			$prevyear = $year - 1;
			$prevmonth = 12;
		}
		//figure out what last month was for each category, and what was this month
		//we're going to copy categories we don't know about to this month.
		//if they were deleted, then we copy them as deleted.
		$sql = "
					select
						c.id,
						c.name,
						cp.id as cp_id,
						c.amount,
						c.fund,
						coalesce(cp2.deleted, c.deleted) as deleted,
						cp2.amount as prevamount
					from
						category c
						left outer join category_period cp
						on
							c.id = cp.category_id
							and cp.period = '$year-$month'
						left outer join category_period cp2
						on
							c.id = cp2.category_id
							and cp2.period = '$prevyear-$prevmonth'
					where
						c.owner_id = $userId
						and cp.id is null and cp2.id is not null";
		$cats = sql_fetch_map($sql, "id");

		//if there isn't something set, set it to last month
		foreach($cats as $id => $data)
		{
			if(!empty($data['prevamount']))
				$amount = $data['prevamount'];
			elseif(!empty($data['amount']))
				$amount = $data['amount'];
			else
				$amount = 0;
			if(!empty($data['deleted']))
				$deleted = $data['deleted'];
			else
				$deleted = 0;
			sql_query("insert into category_period(category_id, period, amount, deleted) values({$data['id']}, '$year-$month', $amount, $deleted)");
		}
		$beginOfMonth = makeDate($year, $month, 1);
		$endOfMonth = makeDate($year, $month, $endday);
		$sql = "
					select 
						c.id, 
						c.name, 
						sum(e.amount / e.span_months * case e.credit when 1 then -1 else 1 end) as total, 
						max(e.id) as expense_id,
						cp.amount, 
						c.fund,
						c.color, 
						case when c.fund = 1 
							then (cp.amount + coalesce(prevcp.amount, 0)) 
								- (coalesce(sum(e.amount * case e.credit when 1 then -1 else 1 end), 0)
									+ coalesce(preve.total, 0))
							else cp.amount - sum(e.amount / e.span_months * case e.credit when 1 then -1 else 1 end) end
						as \"left\",
						sum(e.amount / e.span_months * case e.credit when 1 then -1 else 1 end) as spent
					from
						category c
						inner join category_period cp
						on
							c.id = cp.category_id
							and cp.period = '$year-$month'
						left outer join (select category_id, sum(amount) as amount 
							from category_period 
							where (substr(category_period.period, 0, 5)::int < $year or (substr(category_period.period, 0, 5)::int = $year and substr(category_period.period, 6)::int < $month)) group by category_id) prevcp
						on
							c.id = prevcp.category_id
						left outer join expense e
						on
							e.category_id = c.id
							and e.date + (e.span_months - 1 || ' months')::interval >= '$beginOfMonth' 
							and e.date <= '$endOfMonth'
							and e.deleted = 0
						left outer join (select category_id, sum(amount * case credit when 1 then -1 else 1 end) as total 
							from expense where date < '$beginOfMonth' and deleted = 0 group by category_id) preve
						on
							preve.category_id = c.id
					where cp.deleted = 0
						and c.owner_id = $userId
						and c.id <> 0
					group by
						c.id, c.name, cp.amount, c.color, c.fund, prevcp.amount, preve.total
					order by
						amount desc";
		$cats = sql_fetch_map($sql, 'id');
		$orphans = sql_fetch_map("
					select 
						null as id,
						'Unknown' as name,
						sum(e.amount / e.span_months * case e.credit when 1 then -1 else 1 end) as total,
						0 as amount,
						0 as fund,
						'gray' as color,
						sum(e.amount / e.span_months * case e.credit when 1 then 1 else -1 end)  as \"left\",
						sum(e.amount / e.span_months * case e.credit when 1 then -1 else 1 end) as spent
					from
						expense e
						inner join account a
						on
							e.entered_by = a.id
							and a.owner_id = $userId
					where
						e.category_id is null
						and e.date + (e.span_months - 1 || ' months')::interval >= '$beginOfMonth' 
						and e.date <= '$endOfMonth'
						and e.deleted = 0
					group by e.category_id", 'id');
		$cats = $cats + $orphans;
		return $cats;
	}
	
	function getCategoryMap($userId)
	{
		return sql_fetch_simple_map("select id, name from category where owner_id = $userId", 'id', 'name');
	}

	function getCategoryOptions($userId, $month, $year)
	{
		$cats = sql_fetch_simple_map("select id, name from category where id = 0", 'id', 'name');
		$cats = $cats + sql_fetch_simple_map("					
					select
						c.id,
						c.name
					from
						category c
						inner join category_period cp
						on
							c.id = cp.category_id
							and cp.period = '$year-$month'
					where cp.deleted = 0 and c.owner_id = $userId
					order by
						name", 'id', 'name');
		return $cats + array('NULL' => 'Unknown');
	}
	
	function getCategories($userId)
	{
		return sql_fetch_rows("select id, name, color, amount from category where owner_id = $userId and deleted = 0 order by name");
	}
	
	function getExpenseTotals($userId, $categoryId, $month, $year)
	{
		$endday = Category::getEndDay($month, $year);
		$beginOfMonth = makeDate($year, $month, 1);
		$endOfMonth = makeDate($year, $month, $endday);
		$sql = "select
							sum(e.amount / e.span_months * case e.credit when 1 then -1 else 1 end) as total,
							0 as amount,
							sum(e.amount / e.span_months * case e.credit when 1 then 1 else -1 end) as \"left\",
								--+ case when c.fund = 1 then sum(cp.amount) else 0 end as \"left\",
							sum(e.amount / e.span_months * case e.credit when 1 then -1 else 1 end) as spent
						from
							expense e
							inner join account a
							on
								a.id = e.entered_by
							--inner join category c on
							--	c.id = e.category_id
							--inner join category_period cp on
							--	c.id = cp.category_id
							--	and (substr(cp.period, 0, 4) < $year or (substr(cp.period, 0, 4) < $year and substr(cp.period, 4) <= $month))
						where
							e.category_id = $categoryId
							and e.date + (e.span_months - 1 || ' months')::interval >= '$beginOfMonth' 
							and e.date <= '$endOfMonth'
							and e.deleted = 0
							and a.owner_id = $userId
						group by e.category_id";
		return sql_fetch_one($sql, 'id');
	}

	function getUnusedColors($userId)
	{
		global $strings;
		$answer = array();
		$colors = sql_fetch_simple_map("select distinct color from category where deleted = 0 and owner_id = $userId", 'color', 'color');
		foreach($strings['colors'] as $color => $v)
		{
			if(!isset($colors[$color]))
			{
				$answer[$color] = $color;
			}
		}
		return $answer;
	}

	function getBlankInfo($userId)
	{
		$colors = Category::getUnusedColors($userId);
		$color = reset($colors);
		return array('name' => '', 'amount' => '', 'color' => $color, 'description' => '', 'comments' => '', 'owner_id' => $userId, 'fund' => 0);
	}

	function &insert($info, $month, $year)
	{
		sql_begin_transaction();
		$info['name'] = sql_escape_string($info['name']);
		$info['description'] = sql_escape_string($info['description']);
		$info['comments'] = sql_escape_string($info['comments']);
		$info['color'] = ticks($info['color']);
		$info['fund'] = ticks($info['fund']);
		$catId = sql_insert("insert into category (name, amount, color, description, comments, owner_id, fund) values({$info['name']}, {$info['amount']}, {$info['color']}, {$info['description']}, {$info['comments']}, {$info['owner_id']}, {$info['fund']})", "category_id_seq");
		sql_query("insert into category_period(category_id, period, amount) values($catId, '$year-$month', {$info['amount']})");
		$answer = &new Category($catId);
		sql_commit_transaction();
		return $answer;
	}

	function delete($id, $month, $year)
	{
		//delete means don't use it for new months.
		sql_query("update category_period set deleted = 1 where category_id = $id and period = '$year-$month'");
		sql_query("update category_period set deleted = 1 where category_id = $id");
	}

	function Category($catId)
	{
		$this->id = $catId;
	}

	function getInfo($month, $year)
	{
		$answer = sql_fetch_one("
				select
					c.id, cp.id as period_id, c.name, coalesce(cp.amount, c.amount) as amount, c.color, c.description, c.comments, c.fund
				from
					category c
					left outer join category_period cp
					on
						c.id = cp.category_id
						and cp.period = '$year-$month'
				where
					c.id = {$this->id}");
		if(empty($answer['period_id']))
		{
			$amount = ticks($answer['amount']);
			$answer['period_id'] = sql_insert("insert into category_period (category_id, period, amount) values({$this->id}, '$year-$month', {$amount})", 'category_period_id_seq');
		}
		return $answer;
	}

	function setInfo($info, $month, $year)
	{
		$info['name'] = sql_escape_string($info['name']);
		$info['description'] = sql_escape_string($info['description']);
		$info['comments'] = sql_escape_string($info['comments']);
		$info['color'] = ticks($info['color']);
		$info['fund'] = ticks($info['fund']);
		sql_query("update category set
						name = {$info['name']}, amount = {$info['amount']}, color = {$info['color']}, description = {$info['description']},
						comments = {$info['comments']},
						fund = {$info['fund']}
					where id = {$this->id}");
		$catPeriodId = sql_fetch_one_cell("select id from category_period where category_id = {$this->id} and period = '$year-$month'");
		if($catPeriodId)
		{
			sql_query("update category_period set
							amount = {$info['amount']} where id = $catPeriodId");
		}
		else
		{
			sql_query("insert into category_period (category_id, period, amount) values({$this->id}, '$year-$month', {$info['amount']})");
		}
	}
}

?>
