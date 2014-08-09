<?php


class zone_accounts extends zone
{
	function zone_accounts()
	{
		$this->setZoneParams(array('year', 'month'));
	}
	
	function initPages($params)
	{
		if(!isLoggedIn())
			BaseRedirect('/login');
		$zparams = $this->getZoneParams();
		$this->guiAssign('year', $zparams['year']);
		$this->guiAssign('month', $zparams['month']);
		$this->guiAssign('zoneUrl', $this->getZoneUrl());
	}
	
	function pageEditAccounts($params)
	{
		$user = getLoggedInUser();
		$accounts = $user->getAccountBalances();
		$this->guiAssign('title', 'Edit Accounts');
		$this->guiAssign('accounts', $accounts);
		$this->guiDisplay('editAccounts.tpl');
	}
	
	function postEditAccounts($p, $z)
	{
		$action = getPostText('actionField');
		if($action == 'update')
		{
			$user = getLoggedInUser();
			foreach(getPostText('account') as $id => $data)
			{
				$account = new Account($id);
				$account->setName(stripslashes($data['name']));
				$account->setNumber($data['number']);
				$account->setBalance($data['balance']);
			}
			redirect(VIRTUAL_URL);
		}
	}
	
	function postDeleteAccount($p, $z)
	{
		$user = getLoggedInUser();
		$accountId = getPostInt('account_id');
		$accounts = Account::getAccounts($user->getId());
		if(isset($accounts[$accountId]))
		{
			$account = new Account(getPostInt('account_id'));
			$account->delete();
			echo(json_encode(true));
		}
		else
			echo(json_encode(false));
	}
	
	function postAddAccount($p, $z)
	{
		$user = getLoggedInUser();
		$name = getPostText('name');
		$name = getPostText('number');
		$balance = getPostText('balance');
		$account = $user->addAccount($name, $balance);
		$account->setNumber($balance);
		$accounts = $user->getAccountBalances($user->getId());
		$account = $accounts[$name];
		echo json_encode($account);
	}
}
