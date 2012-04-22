<?php

class user
{
	//static
	function getNewUser($userInfo)
	{
		$id = sql_fetch_one_cell("select id from person where email = '{$userInfo['email']}'");
		if($id !== false)
			return false;
		$userInfo['password'] = md5($userInfo['password']);
		$id = sql_insert("insert into person (email, password) values('{$userInfo['email']}', '{$userInfo['password']}')", 'person_id_seq');
		return new User($id);
	}
	
	//static
	function login($email, $password)
	{
		$password = md5($password);
		$id = sql_fetch_one_cell("select id from person where email = '$email' and password = '$password'");
		if($id === false)
			return false;
		return new user($id);
	}
	
	function user($id)
	{
		$this->id = $id;
	}
	
	function getId()
	{
		return $this->id;
	}
	
	function isInitDone()
	{
		return sql_fetch_one_cell("select init_done from person where id = {$this->id}");
	}
	
	function setInitDone()
	{
		return sql_query("update person set init_done = 1 where id = {$this->id}");
	}
	
	function getAccountBalances()
	{
		//get accounts
		return account::getAccountBalances($this->id);
	}
	
	function addAccount($name, $balance)
	{
		$acc = new account(account::getIdByName($this->id, $name));
		$acc->setBalance($balance);
	}
	
	function addCategory($categoryInfo, $month, $year)
	{
		$categoryInfo['owner_id'] = $this->id;
		return category::insert($categoryInfo, $month, $year);
	}
	
	function getCategoryMap()
	{
		//get categories
		return category::getCategoryMap($this->id);
	}
	
	function getCategories()
	{
		//get categories
		return category::getCategories($this->id);
	}
}