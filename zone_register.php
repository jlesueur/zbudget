<?php

class zone_register extends zone
{
	function zone_register()
	{
	}
	
	function makePath()
	{
		return '/register';
	}
	
	function initZone($p)
	{
	}
	
	function initPages($p)
	{
		$this->guiAssign('zoneUrl', $this->getZoneUrl());
	}
	
	function pageDefault($p)
	{
		if(isset($p[1]))
		{
			$error = $p[1];
			$this->guiAssign('error', $error);
		}
		$email = &getGuiControl('text', 'Email');
		$email->setRequired();
		$password = &getGuiControl('password', 'Password');
		$password->setRequired();
		$password2 = &getGuiControl('password', 'Password2');
		$password2->setRequired();
		$user = $this->session('user');
		if($user != false)
		{
			$email->setValue($user['email']);
			$password->setValue($user['password']);
		}
		$secretcode = &getGuiControl('text', 'Secret Code');
		$secretcode->setRequired();
		$this->guiAssign('title', 'Register for zBudget');
		$this->guiAssign('email', $email);
		$this->guiAssign('password', $password);
		$this->guiAssign('password2', $password2);
		$this->guiAssign('secretcode', $secretcode);
		$this->guiDisplay('default.tpl');
	}
	
	function postDefault($p)
	{
		global $secretCodes;
		$action = getPostText('actionField');
		if($action == 'register')
		{
			sql_begin_transaction();
			$user['email'] = getPostText('Email');
			$user['password'] = getPostText('Password');
			if(getPostText('Password') != getPostText('Password2'))
			{
				$this->session('user', $user);
				$this->zoneRedirect('password');
			}
			if(in_array(getPostText('Secret Code'), $secretCodes))
				$newUser = user::getNewUser($user);
			else
			{
				$this->session('user', $user);
				$this->zoneRedirect('secretcode');
			}
			if($newUser == false)
			{
				$this->session('user', $user);
				$this->zoneRedirect('email');
			}
			setLoggedInUser($newUser);
			sql_commit_transaction();
			$this->zoneRedirect('initAccounts');
		}
	}
	
	function pageEmailPassword($p)
	{
		//email a password
	}
	
	function pageInitAccounts($p)
	{
		requireLoggedIn();
		$user = getLoggedInUser();
		$this->guiAssign('title', 'Enter Initial Account and Budget Information');
		$this->assignAccounts();
		$this->guiDisplay('initAccounts.tpl');
	}
	
	function postInitAccounts($p)
	{
		$user = getLoggedInUser();
		$action = getPostText('actionField');
		if($action == 'skip')
		{
			if(!sql_check("select id from account where owner_id = {$user->getId()}"))
				$user->addAccount('default', 0);
			$this->zoneRedirect('initCategories');
		}
		else if($action == 'upload')
		{
			sql_begin_transaction();
			$csvfile = &new OfxExpenseParser();
			$csvfile->open($_FILES['importFile']['tmp_name']);
			$prevAccount = null;
			$prevAccountId = null;
			while(($transaction = $csvfile->getTransaction()) !== false)
			{
				$expense = &Expense::ImportTransaction($transaction, $user->id);
				if($prevAccount === null && $prevAccountId === null)
				{
					$account = $expense->getAccount();
					$prevAccount = $csvfile->enteredBy;
					$prevAccountId = $csvfile->enteredById;
					//$balance = $csvfile->balance;
				}
				if(!empty($csvfile->enteredBy) && $csvfile->enteredBy != $prevAccount)
				{
					$account->setBalance($balance);
					$account = $expense->getAccount();
					$prevAccount = $csvfile->enteredBy;
					//$balance = $csvfile->balance;
				}
				else if(!empty($csvfile->enteredById) && $csvfile->enteredById != $prevAccountId)
				{
					$account->setBalance($balance);
					$account = $expense->getAccount();
					$prevAccountId = $csvfile->enteredById;
					//$balance = $csvfile->balance;
				}
			}
			//$account->setBalance($balance);
			$account = $expense->getAccount();
			sql_commit_transaction();
			$this->zoneRedirect('initAccounts');
		}
		else if($action == 'update')
		{
			foreach(getPostText('account') as $id => $data)
			{
				$account = new Account($id);
				$account->setName($data['name']);
				$account->setNumber($data['number']);
				$account->setBalance($data['balance']);
			}
			redirect(VIRTUAL_URL);
		}
		else if($action == 'done')
		{
			$this->zoneRedirect('initCategories');
		}
	}
	
	function pageDisplayAccounts($p)
	{
		requireLoggedIn();
		//display accounts
		$this->assignAccounts();
		$this->guiDisplay('displayAccounts.tpl');
	}
	
	function postAddAccount($p)
	{
		requireLoggedIn();
		//dump_r(getRawPost());
		//add an account to the user...
		$user = getLoggedInUser();
		$account = $user->addAccount(getPostText('name'));
		$account->setBalance(getPostInt('balance'));
		$this->zoneRedirect('displayAccounts');
	}
	
	function assignAccounts()
	{
		$user = getLoggedInUser();
		$this->guiAssign('accounts', $user->getAccountBalances());
	}
	
	function pageInitCategories($p)
	{
		$this->guiAssign('title', 'Enter Initial Account and Budget Information');
		$this->assignCategories();
		$this->guiDisplay('initCategories.tpl');
	}
	
	function postInitCategories($p)
	{
		$user = getLoggedInUser();
		$action = getPostText('actionField');
		if($action == 'done')
		{
			$user->setInitDone();
			BaseRedirect('');
		}
	}
	
	function pageDisplayCategories($p)
	{
		requireLoggedIn();
		$this->assignCategories();
		$this->guiDisplay('displayCategories.tpl');
	}
	
	function postAddCategory($p)
	{
		requireLoggedIn();
		$user = getLoggedInUser();
		$categoryInfo = category::getBlankInfo($user->getId());
		$categoryInfo['name'] = getPostText('name');
		$categoryInfo['color'] = getPostText('color');
		$categoryInfo['amount'] = getPostText('amount');
		$user->addCategory($categoryInfo, (int)date('m'), date('Y'));
		$this->zoneRedirect('displayCategories');
	}
	
	function assignCategories()
	{
		global $strings;
		$user = getLoggedInUser();
		$unused = Category::getUnusedColors($user->id);
		foreach($strings['colors'] as $color => $value)
		{
			if(isset($unused[$color]))
				$colors[$color] = $color . ' (unused)';
			else
				$colors[$color] = $color;
		}
		$this->guiAssign('colors', $colors);
		$this->guiAssign('categoryInfo', category::getBlankInfo($user->getId()));
		$this->guiAssign('categories', $user->getCategories());
	}
}