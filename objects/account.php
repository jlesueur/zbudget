<?php

class account
{
	//static
	function getAccountMap($userId)
	{
		return sql_fetch_simple_map("select id, name from account where owner_id = $userId and deleted = 0", 'id', 'name');
	}
	
	function getAccounts($userId)
	{
		return sql_fetch_map("select id, name from account where owner_id = $userId", 'id');
	}
	
	function getIdByName($userId, $name, $escaped = 0)
	{
		if(!$escaped)
			$name = sql_escape_string($name, 0);
		$id = sql_fetch_one_cell("select id from account where name like $name and owner_id = $userId");
		if($id === false)
		{
			$sql = "insert into account (name, owner_id, deleted) values($name, $userId, 0)";
			$id = sql_insert($sql, 'account_id_seq');
		}
		return $id;
	}
	
	function getIdByNumber($userId, $number, $escaped = 0)
	{
		if(!$escaped)
			$number = sql_escape_string($number, 0);
		$id = sql_fetch_one_cell("select id from account where number like $number and owner_id = $userId");
		if($id === false)
		{
			$sql = "insert into account (number, owner_id, deleted) values($number, $userId, 0)";
			$id = sql_insert($sql, 'account_id_seq');
		}
		return $id;
	}
	
	function getAccountBalances($userId, $endDate = NULL)
	{
		if($endDate == NULL)
		{
			$endDate = date('Y-m-d');
		}
		$sql = "
			select 
				account.id,
				account.name as account,
				account.number as number,
				sum(case when expense.credit = 1 then amount else amount * -1 end) as balance
			from 
				account
				left outer join expense 
				on 
					expense.entered_by = account.id
					and expense.date <= '$endDate'
					and expense.deleted = 0
			where 
				owner_id = $userId
				and account.deleted = 0
			group by
				account.id,
				account.name,
				account.number
			order by account.name";
		return sql_fetch_map($sql, 'account');
	}
	
	//static
	function getBlankInfo()
	{
		return array('name' => '', 'owner_id' => '');
	}
	
	function Account($id)
	{
		$this->id = $id;
	}
	
	function getName()
	{
		$sql = "select name from account where id = $this->id";
		return sql_fetch_one_cell($sql);
	}
	
	function setName($name)
	{
		$name = sql_escape_string($name);
		$sql = "update account set name = $name where id = {$this->id}";
		sql_query($sql);
	}
	
	function setNumber($number)
	{
		$number = sql_escape_string($number);
		$sql = "update account set number = $number where id = {$this->id}";
		sql_query($sql);
	}
	
	function getBalance($endDate = NULL)
	{
		if($endDate == NULL)
		{
			$endDate = date('Y-m-d');
		}
		$balance = sql_fetch_one_cell("	
			select 
				sum(case when expense.credit = 1 then amount else amount * -1 end) as balance
			from 
				account
				left outer join 
					expense 
				on 
					expense.entered_by = account.id
					and expense.deleted = 0
			where 
				account.id = $this->id
			group by
				account.id");
		return $balance;
	}
	
	function setBalance($amount)
	{
		$currBalance = $this->getBalance();
		if($currBalance != $amount)
		{
			$expense = expense::getBlankInfo();
			$expense['category_id'] = 0;
			$expense['date'] = makeDate('2000', '01', '01');
			$expense['amount'] = $amount - $currBalance;
			$expense['entered_by'] = $this->id;
			$expense['store'] = 'Initial Balance';
			$expense['credit'] = 1;
			$expense = expense::insert($expense);
		}
	}
	
	function delete()
	{
		sql_query("update account set deleted = 1 where id = {$this->id}");
	}
}