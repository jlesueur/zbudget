<?php

class Expense
{
	//static
	function &importTransaction($transaction, $userId)
	{
		if(!empty($transaction['entered_by']))
			$enteredBy = account::getIdByName($userId, sql_escape_string($transaction['entered_by'], 0), true);
		else if(!empty($transaction['entered_byId']))
			$enteredBy = account::getIdByNumber($userId, sql_escape_string($transaction['entered_byId'], 0), true);
		$transaction['amount'] = number_format($transaction['amount'], 2, '.', '');//make sure that numbers are consistent format.
		$uniqueId = md5($transaction['store'] . $transaction['amount'] . $transaction['date'] . $transaction['credit'] . $enteredBy);
		$unique = sql_fetch_one_cell("select id from expense where unique_id = '$uniqueId'");
		if($unique !== false)
		{
			$expense = &new Expense($unique);
			return $expense;
		}
		$store = $transaction['store'];
		if(!isset($transaction['comment']))
			$transaction['comment'] = '';
		foreach($transaction as $field => $value)
		{
			$transaction[$field] = sql_escape_string($value, 0);
		}
		$sql = "insert into expense (amount, store, entered_by, date, credit, comment, unique_id) values({$transaction['amount']}, {$transaction['store']}, $enteredBy, {$transaction['date']}, {$transaction['credit']}, {$transaction['comment']}, '$uniqueId')";
		sql_query($sql);
		$id = sql_fetch_one_cell("select id from expense where unique_id = '$uniqueId'");
		$expense = &new Expense($id);
		$expense->applyStoreCategoryMatches($store, $userId, $expense);
		/*
		sql_query("update expense 
			set category_id = sc.category_id 
			from 
				store_category sc inner join 
				category on 
					sc.category_id = category.id 
					and category.owner_id = $userId 
				where 
					expense.store like '%' || sc.store || '%' and 
					expense.category_id is null 
					and expense.id = {$id}");
		*/
		return $expense;
	}
	
	//static 
	function applyStoreCategoryMatches($store, $userId, $expense)
	{
		static $storeCategories = NULL;
		if($storeCategories == NULL)
			$storeCategories = sql_fetch_simple_map("select 
				category_id, store 
			from 
				store_category
			where 
				store_category.owner_id = {$userId}
			order by
				store_category.list_order", "store", "category_id");
		foreach($storeCategories as $storeExp => $categoryId)
		{
			if(preg_match('/' . $storeExp . '/', $store))
			{
				if(is_NULL($categoryId))
					$categoryId = 'NULL';
				$expense->setBasicItem('category_id', $categoryId);
				break;
			}
		}
	}

	//static
	function insert($info)
	{
		foreach($info as $field => $data)
		{
			$fields[] = $field;
			$values[] = sql_escape_string($data);
		}
		$fields = implode(', ', $fields);
		$values = implode(', ', $values);
		$sql = "insert into expense ($fields) values($values)";
		$id = sql_insert($sql, 'expense_id_seq');
		return $id;
	}

	//static
	function &infoFromObjects($userId, &$objs)
	{
		foreach($objs as $obj)
		{
			$ids[] = $obj->id;
		}
		$ids = implode(', ', $ids);
		$sql = "select  
					e.id,
					c.id as c_id,
					c.name as category,
					c.color as color,
					(e.amount / span_months) as amount,
					e.deleted as deleted,
					e.store,
					coalesce(a.name, a.number) as entered_by,
					e.date,
					e.credit,
					(select 
						sum(case when expense.credit = 1 then amount else amount * -1 end) 
						from expense 
							inner join account on account.deleted = 0 and account.owner_id = $userId and expense.entered_by = account.id
						where 
							(expense.date < e.date or (expense.date <= e.date and expense.id <= e.id)) 
							and e.entered_by = expense.entered_by and account.owner_id = $userId and expense.deleted = 0) as thisbalance,
					(select 
						sum(case when expense.credit = 1 then amount else amount * -1 end) 
						from expense 
							inner join account on account.deleted = 0 and account.owner_id = $userId and expense.entered_by = account.id
						where (expense.date < e.date  or (expense.date <= e.date and expense.id <= e.id))
							 and expense.deleted = 0) as balance
				from
					expense e
					inner join account a
					on
						e.entered_by = a.id
						and a.owner_id = $userId
					left outer join category c
					on
						e.category_id = c.id
						and c.owner_id = $userId
				where
					e.id in ($ids)
					and e.deleted = 0";
		$answer = sql_fetch_map($sql, 'id');
		return $answer;
	}
	
	//static
	function &getExpenseList($userId, $month, $year, $searchValues)
	{
		$endday = Category::getEndDay($month, $year);
		$searchWhere[] = "1 = 1";
		$blankInfo = expense::getBlankInfo();
		foreach($searchValues as $name => $value)
		{
			if(array_key_exists($name, $blankInfo))
			{
				if($value != 'NULL')
					$searchWhere[] = "e.$name = $value";
				else
					$searchWhere[] = "e.$name is $value";
			}
		}
		$searchWhere = implode (' AND ', $searchWhere);
		$sql = "select  
						e.id,
						c.id as c_id,
						c.name as category,
						c.color as color,
						(e.amount / span_months) as amount,
						e.deleted as deleted,
						e.store,
						a.name as entered_by,
						e.date,
						e.credit,
						(select 
							sum(case when expense.credit = 1 then amount else amount * -1 end) 
							from expense 
								inner join account on account.deleted = 0 and account.owner_id = $userId and expense.entered_by = account.id
							where 
								(expense.date < e.date or (expense.date <= e.date and expense.id <= e.id)) 
								and e.entered_by = expense.entered_by and account.owner_id = $userId and expense.deleted = 0) as thisbalance,
						(select 
							sum(case when expense.credit = 1 then amount else amount * -1 end) 
							from expense 
								inner join account on account.deleted = 0 and account.owner_id = $userId and expense.entered_by = account.id
							where (expense.date < e.date  or (expense.date <= e.date and expense.id <= e.id))
								 and expense.deleted = 0) as balance
					from
						expense e
						inner join account a
						on
							e.entered_by = a.id
							and a.owner_id = $userId
						left outer join category c
						on
							e.category_id = c.id
							and (c.owner_id = $userId OR c.id = 0)
					where
						date + (span_months - 1 || ' months')::interval >= '$year-$month-01' 
						and date <= '$year-$month-$endday'
						and e.deleted = 0
						and $searchWhere
					order by date, id";
		$answer = sql_fetch_map($sql, "id");
		
		return $answer;
	}

	//static
	function &getExpenseListByCatId($month, $year, $catId)
	{
		$endday = Category::getEndDay($month, $year);
		$sql = "select  
						e.id, 
						c.id as c_id,
						c.name as category,
						c.color as color,
						(e.amount / span_months) as amount,
						e.deleted as deleted,
						e.store,
						a.name as entered_by,
						e.date
					from
						expense e
						inner join account a
						on
							e.entered_by = a.id
						left outer join category c
						on
							e.category_id = c.id
					where 
						date + (span_months - 1 || ' months')::interval >= '$year-$month-01' 
						and date < '$year-$month-$endday'
						and e.deleted = 0
						and c.id = $catId
					order by date";
		$answer = sql_fetch_map($sql, "id");
		return $answer;
	}
	
	//static
	function getCashFlow($userId, $startDate, $endDate = NULL)
	{
		if($endDate == NULL)
		{
			$endDate = date('Y-m-d');
		}
		$sql = "
				select * from (
				select 
					account.name,
					expense.date,
					(select 
						sum(case when e.credit = 1 then amount else amount * -1 end) 
					from expense e
						inner join account a on a.deleted = 0 and e.entered_by = a.id
					where 
						(e.date <= expense.date) 
						and e.entered_by = account.id
						and e.deleted = 0) as balance
						
				from 
					expense
					inner join account
					on
						expense.entered_by = account.id
						and account.deleted = 0
				where 
					date between '$startDate' and '$endDate'
					and expense.deleted = 0
					and account.owner_id = $userId
				group by expense.date, account.name, account.id
				" . 
				/*union 
				select 
					'Total' as name,
					expense.date,
					(select 
						sum(case when e.credit = 1 then amount else amount * -1 end) 
					from expense e
						inner join account a on a.deleted = 0 and e.entered_by = a.id
					where 
						(e.date <= expense.date) 
						and e.deleted = 0) as balance
				from
					expense
				where 
					date between '$startDate' and '$endDate'
					and expense.deleted = 0
				group by expense.date
				*/
				") foo order by date
				";
		$rows = sql_fetch_map($sql, array('date', 'name'));
		$starttime = strtotime($startDate);
		$endtime = strtotime($endDate);
		$prevdate = $startDate;
		while($starttime != $endtime && !empty($rows))
		{
			$starttime = strtotime("tomorrow", $starttime);
			$curdate = date('Y-m-d', $starttime);
			if(isset($rows[$prevdate]))
			{
				foreach($rows[$prevdate] as $account)
				{
					if(!isset($rows[$curdate]) || !isset($rows[$curdate][$account['name']]))
					{
						$rows[$curdate][$account['name']] = $account;
					}
				}
			}
			$prevdate = $curdate;
		}
		$starttime = strtotime($endDate);
		$endtime = strtotime($startDate);
		$prevdate = $endDate;
		while($starttime != $endtime)
		{
			$starttime = strtotime("yesterday", $starttime);
			$curdate = date('Y-m-d', $starttime);
			if(isset($rows[$prevdate]))
			{
				foreach($rows[$prevdate] as $account)
				{
					if(!isset($rows[$curdate]) || !isset($rows[$curdate][$account['name']]))
					{
						$rows[$curdate][$account['name']] = $account;
					}
				}
			}
			$prevdate = $curdate;
		}
		
		ksort($rows);
		return $rows;
	}
		
		
	
	//static 
	function getBlankInfo()
	{
		return array('date' => null, 'amount' => null, 'comment' => null, 'store' => null,
			'entered_by' => null, 'category_id' => null, 'span_months' => 1, 'credit' => 0);
	}
	
	function Expense($id)
	{
		$this->id = $id;
	}

	function setBasicItem($field, $value)
	{
		if(in_array($field, array('date', 'store', 'comment')))
			$value = sql_escape_string($value);
		sql_query("update expense set $field = $value where id = $this->id");
	}

	function getInfo()
	{
		$sql = "select * from expense where id = {$this->id}";
		$info = sql_fetch_one($sql);
		return $info;
	}
	
	function setInfo($info)
	{
		$info['date'] = ticks($info['date']);
		$info['store'] = ticks($info['store']);
		$info['comment'] = ticks($info['comment']);
		sql_query("update expense set
						date = {$info['date']}, store = {$info['store']}, comment = {$info['comment']}, category_id = {$info['category_id']},
						entered_by = {$info['entered_by']}, amount = {$info['amount']}, span_months = {$info['span_months']}, credit = {$info['credit']}
					where id = {$this->id}");
	}
	
	function getAccount()
	{
		$accountId = sql_fetch_one_cell("select entered_by from expense where id = $this->id");
		return new account($accountId);
	}
	
	function delete()
	{
		$this->setBasicItem('deleted', 1);
	}
	
	function guessCategory()
	{
		sql_query("update expense set category_id = sc.category_id from store_category sc where expense.store like '%' || sc.store || '%' and expense.category_id is null and expense.id = {$this->id}");
	}
}
