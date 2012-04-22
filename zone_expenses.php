<?php

class zone_expenses extends zone_sequence
{

	function zone_expenses()
	{
		//nothing
		$this->setZoneParams(array('year', 'month'));
	}
	
	function makePath($year, $month)
	{
		return "/expenses/$year/$month";
	}
	
	function initZone($params)
	{
		//do something
	}
	
	function initPages($params)
	{
		//do something
		$this->guiAssign('zoneUrl', $this->getZoneUrl());
	}
	
	function pageDefault($params)
	{
		//do something
		/*
		$month = (int)date('m');
		$year = date('Y');
		$this->zoneRedirect("list/$year/$month");
		*/
		$this->zoneRedirect('list');
		
	}
	
	function getExpenseSearchParams($month, $year)
	{
		$user = getLoggedInUser();
		return array('category_id' => array('type' => 'options', 'options' => Category::getCategoryOptions($user->getId(), $month, $year), 'displayName' => 'Category:'),
					'entered_by' => array('type' => 'options', 'options' => account::getAccountMap($user->GetId()), 'displayName' => 'Account:'),
					'iterations' => array('type' => 'options', 'options' => array(15=>15, 30=>30, 50 => 50, 'All' => 'All'), 'hideAny' => 'true', 'displayName' => 'Show:'),
					);
	}
	
	function pageList($params)
	{
		$user = getLoggedInUser();
		$searchValues = array();
		for($i = 1; $i + 1 < count($params); $i+=2)
		{
			$searchValues[$params[$i]] = $params[$i+1];
		}
		$year = $this->getZoneParam('year');
		$month = $this->getZoneParam('month');
		$expenses = Expense::getExpenseList($user->getId(), $month, $year, $searchValues);
		$categoryOptions = Category::getCategoryOptions($user->getId(), $month, $year);
		$this->guiAssign('categoryOptions', $categoryOptions);//for the drop down category menus
		$this->assignBudgetList($month, $year);
		$searchParams = $this->getExpenseSearchParams($month, $year);
		$this->guiAssign('searchParams', $searchParams);
		$this->guiAssign('searchValues', $searchValues);
		$this->guiAssign('iterations', isset($searchValues['iterations']) ? ($searchValues['iterations'] == 'All' ? 1000 :$searchValues['iterations']) : 15);
		$this->guiAssign('month', $month);
		$prevmonth = $month -1;
		$prevyear = $year;
		if($prevmonth == 0)
		{
			$prevmonth = 12;
			$prevyear = $year - 1;
		}
		$nextmonth = $month + 1;
		$nextyear = $year;
		if($nextmonth == 13)
		{
			$nextmonth = 1;
			$nextyear = $year + 1;
		}
		$this->guiAssign('prevPeriod', SCRIPT_URL . zone_expenses::makePath($prevyear, $prevmonth) . '/' . implode('/', $params));
		$this->guiAssign('nextPeriod', SCRIPT_URL . zone_expenses::makePath($nextyear, $nextmonth) . '/' . implode('/', $params));
		$this->guiAssign('year', $year);
		
		$this->guiAssign('expenses', $expenses);
		$this->guiDisplay('list.tpl');
	}
	
	function assignBudgetList($month, $year)
	{
		$user = getLoggedInUser();
		$categories = Category::getBudgetList($user->getId(), $month, $year);
		$totals['total'] = 0;
		$totals['amount'] = 0;
		$totals['left'] = 0;
		foreach($categories as $id => $data)
		{
			if(!$data['fund'])//envelopes don't contribute to totals...
			{
				$totals['total'] += $data['total'];//how much have we spent
				$totals['amount'] += $data['amount'];//how much did we allocate
				$totals['left'] += empty($data['left']) ? $data['amount'] : $data['left'];//how much is left
			}
		}
		$unbudget = Category::getExpenseTotals($user->getId(), 0, $month, $year);
		$flow = ($unbudget['total'] + $totals['total']) * -1;
		$unbudget['total'] = $unbudget['total'] * -1;
		$accountTotals = account::getAccountBalances($user->getId(), "$year-$month-" . Category::getEndDay($month, $year));
		$tmp = 0;
		foreach($accountTotals as $account)
		{
			$tmp += $account['balance'];
		}
		$accountTotals[] = array('account' => 'Total', 'balance' => number_format($tmp, 2, '.', ''));
		$this->guiAssign('accountTotals', $accountTotals);
		$this->guiAssign('flow', $flow);
		$this->guiAssign('unbudget', $unbudget);
		$this->guiAssign('total', $totals);
		$this->guiAssign('categories', $categories);//for the budget list
	}
	
	function pageBudgetList($params)
	{
		global $gui;
		$year = $this->getZoneParam('year');
		$month = $this->getZoneParam('month');
		$this->assignBudgetList($month, $year);
		$this->guiAssign('year', $year);
		$this->guiAssign('month', $month);
		$gui->display('budgetList.tpl');
		die();
	}
	
	function postList($params)
	{
		$year = $this->getZoneParam('year');
		$month = $this->getZoneParam('month');
		$action = getPostText('actionField');
		if($action == 'editCat')
		{
			$catId = getPostInt('catId');
			BaseRedirect("/budget/editCat/$catId/$year/$month");
		}
		else if ($action == 'editExpense')
		{
			$expenseId = getPostInt('expenseId');
			$this->zoneRedirect("edit/$expenseId");
		}
		else if ($action == 'deleteExpense')
		{
			$expenseId = getPostInt('expenseId');
			$expense = &new Expense($expenseId);
			$expense->delete();
			redirect(VIRTUAL_URL);
		}
		else if($action == 'search')
		{
			$params = $this->getExpenseSearchParams($month, $year);
			$searchValues = array();
			foreach($params as $name => $param)
			{
				$tmpValue = getPostText($name);
				if($tmpValue !== '')
					$searchValues[$name] = $tmpValue;
			}
			$url = $this->getZoneUrl() . '/list';
			foreach($searchValues as $name => $value)
			{
				$url .= "/$name/$value";
			}
			redirect($url);
		}
	}
	
	function pageEdit($params)
	{
		global $gui;
		$year = $this->getZoneParam('year');
		$month = $this->getZoneParam('month');
		$user = getLoggedInUser();
		$defaults = $this->session('defaults');
		if($defaults === null)
			$defaults = array();
		$expenseId = $params[1];
		if($expenseId == 'new')
			$expenseInfo = array_merge(Expense::getBlankInfo(), $defaults);
		else
		{
			$expense = &new Expense($expenseId);
			$expenseInfo = $expense->getInfo();
		}
		//TODO: only show categories that are valid for the date selected by the user. Make user choose date first, then choose category?
		//TODO: then use Category::getCategoryOptions()
		$categories = Category::getCategoryOptions($user->getId(), $month, $year);
		$this->guiAssign('categories', $categories);
		//TODO: use a person object...
		$accounts = account::getAccountMap($user->getId());
		
		//make guicontrols...
		$date = &getGuiControl('dateControl', 'date');
		$date->setValue($expenseInfo['date']);
		$date->setRequired();
		$date->setValidationType('date');
		$this->guiAssign("date", $date);
		
		//TODO: create a custom guicontainer that encapsulates the choosing of a date and a category...
		
		$amount = &getGuiControl('text', 'amount');
		$amount->setValidationType('money');
		$amount->setRequired();
		$amount->setValue($expenseInfo['amount']);
		$this->guiAssign('amount', $amount);
		
		$credit = &getGuiControl('radio', 'credit');
		$credit->setParam('index', array(0=>'debit', 1=>'credit'));
		$credit->setRequired();
		$credit->setValue($expenseInfo['credit']);
		$this->guiAssign('credit', $credit);
		
		$comment = &getGuiControl('textarea', 'comment');
		$comment->setValue($expenseInfo['comment']);
		$this->guiAssign('comment', $comment);
		
		$store = &getGuiControl('text', 'store');
		$store->setValue($expenseInfo['store']);
		$this->guiAssign('store', $store);
		
		$enteredBy = &getGuiControl('select', 'entered_by');
		$enteredBy->setParam('displayName', 'Entered By');
		$enteredBy->setValue($expenseInfo['entered_by']);
		$enteredBy->setParam('index', $accounts);
		$this->guiAssign('enteredBy', $enteredBy);
		
		$this->guiAssign('expenseInfo', $expenseInfo);
		$this->guiDisplay('edit.tpl');
	}
	
	function postEdit($params)
	{
		//gather expenseInfo into an array
		$action = getPostText('actionField');
		if($action != 'cancel')
		{
			$defaults['date'] = $expenseInfo['date'] = getPostText('date');
			$defaults['category_id'] = $expenseInfo['category_id'] = getPostInt('category_id');
			$defaults['entered_by'] = $expenseInfo['entered_by'] = getPostInt('entered_by');
			$this->session('defaults', $defaults);
			$expenseInfo['store'] = getPostText('store');
			$expenseInfo['amount'] = getPostText('amount');
			$expenseInfo['comment'] = getPostText('comment');
			$expenseInfo['credit'] = getPostInt('credit');
			if(getPostIsset('repeat'))
				$expenseInfo['span_months'] = getPostInt('span_months');
			else
				$expenseInfo['span_months'] = 1;
			if($params[1] === 'new')
			{
				Expense::insert($expenseInfo);
			}
			else
			{
				$expense = &new Expense($params[1]);
				$expense->setInfo($expenseInfo);
			}
			$date = explode('-', $defaults['date']);
			$year = $date[0];
			$month = (int)$date[1];
		}
		else
		{
			$defaults = $this->session('defaults');
			$date = explode('-', $defaults['date']);
			$year = $date[0];
			$month = (int)$date[1];
		}
		switch($action)
		{
			case 'save':
			case 'cancel':
				$this->zoneRedirect("list");
				break;
			case 'split':
				$this->zoneRedirect("split/{$params[1]}");
				break;
			case 'continue':
			case 'default':
				$this->zoneRedirect('edit/new');
				break;
			default:
				$this->zoneRedirect("list");
				break;
		}
	}
	
	function pageImport($params)
	{
		//display a form to upload a file
		$user = getLoggedInUser();
		$this->guiAssign('accounts', account::getAccountMap($user->getId()));
		$this->guiDisplay("import.tpl");
	}
	
	function postImport($params)
	{
		$action = getPostText('actionField');
		if($action == 'cancel')
			$this->zoneRedirect('list');
		global $sGlobals;
		$user = getLoggedInUser();
		//read a csv file
		if(getPostText('format') == 'zionsbank')
		{
			$csvfile = &new CsvExpenseParser();
		}
		else if(getPostText('format') == 'wellsfargo')
		{
			$csvfile = &new WFExpenseParser();
		}
		else if(getPostText('format') == 'mbna')
		{
			assert(getPostInt('account_id') !== '');
			$account = &new Account(getPostInt('account_id'));
			$csvfile = &new MBNAExpenseParser($account);
		}
		else if(getPostText('format') == 'capitalone')
		{
			assert(getPostInt('account_id') !== '');
			$account = &new Account(getPostInt('account_id'));
			$csvfile = &new CapitalOneExpenseParser($account);
		}
		else if(getPostText('format') == 'ofx')
		{
			$csvfile = &new OfxExpenseParser();
		}
		$csvfile->open($_FILES['importFile']['tmp_name']);
		sql_begin_transaction();
		$sGlobals->importTransaction = array();
		while(($transaction = $csvfile->getTransaction()) !== false)
		{
			$sGlobals->importExpenses[] = &Expense::ImportTransaction($transaction, $user->id);
		}
		//die();
		sql_commit_transaction();
		$this->zoneRedirect('listImport');
		/*
		$qif = &new QifParser();
		$qif->parse($file);
		foreach($qif->getTransactions() as $transact)
		{
			$expense = &Expense::ImportTransaction($transact);
		}
		*/
		/*
		$info = $expense->getInfo();
		$date = $info['date'];
		$date = explode('-', $date);
		$year = $date[0];
		$month = $date[1];
		die("list/$year/$month");
		$this->zoneRedirect("list/$year/$month");
		*/
	}
	
	function pageListImport($params)
	{
		global $sGlobals;
		$year = $this->getZoneParam('year');
		$month = $this->getZoneParam('month');
		$user = getLoggedInUser();
		$expenses = $sGlobals->importExpenses;
		$expenses = expense::InfoFromObjects($user->getId(), $expenses);
		$categoryOptions = Category::getCategoryOptions($user->getId(), $month, $year);
		$this->guiAssign('title', 'Imported Expenses');
		$this->guiAssign('month', $month);
		$this->guiAssign('year', $year);
		$this->guiAssign('categoryOptions', $categoryOptions);
		$this->guiAssign('iterations', 100);
		$this->guiAssign('expenses' , $expenses);
		$this->guiDisplay('listImport.tpl');
	}
	
	function postListImport($params)
	{
		global $sGlobals;
		unset($sGlobals->importExpenses);
		$this->zoneRedirect('list');
	}
	
	function postChangeExpenseCategory($params)
	{
		$year = $this->getZoneParam('year');
		$month = $this->getZoneParam('month');
		$expenseId = $params[1];
		$categoryId = $params[2];
		if($categoryId == '')
			$categoryId = 'NULL';
		$expense = &new Expense($expenseId);
		$expense->setBasicItem('category_id', $categoryId);
		if($categoryId != 'NULL')
		{
			$category = &new Category($categoryId);
			$catInfo = $category->getInfo($month, $year);
			echo(json_encode($catInfo));
		}
		die();
	}
	
	function pageSplit($params)
	{
		//side by side, one expense with grayed out parts, that is current expense,
		//one expense that is copied from current expense, but amount is 0. 
		//as amount is changed in one, the amount in grayed out will be changed.
		global $gui;
		$year = $this->getZoneParam('year');
		$month = $this->getZoneParam('month');
		$user = getLoggedInUser();
		$expenseId = (int)$params[1];
		assert(!empty($expenseId));
		$expense = &new Expense($expenseId);
		$this->guiAssign('expense', $expense);
		$expenseInfo = $expense->getInfo();
		$this->guiAssign('expenseInfo', $expenseInfo);
		//TODO: only show categories that are valid for the date selected by the user. Make user choose date first, then choose category?
		//TODO: then use Category::getCategoryOptions()
		$categories = Category::getCategoryOptions($user->getId(), $month, $year);
		$this->guiAssign('categories', $categories);
		//TODO: use a person object...
		$accounts = account::getAccountMap($user->getId());
		$this->guiAssign('accounts', $accounts);
		
		$comment = &getGuiControl('textarea', 'comment');
		$comment->setValue($expenseInfo['comment']);
		$this->guiAssign('comment', $comment);
		
		$this->guiDisplay('split.tpl');
	}
	
	function postSplit($params, $z)
	{
		$expenseId = (int)$params[1];
		$action = getPostText('actionField');
		if($action != 'cancel')
		{
			sql_begin_transaction();
			$expense = &new Expense($expenseId);
			$expenseInfo = $expense->getInfo();
			$expenseInfo['amount'] = getPostText('newAmount');
			$expense->setInfo($expenseInfo);
			$expenseInfo['amount'] = getPostText('amount');
			$expenseInfo['category_id'] = getPostInt('category_id');
			unset($expenseInfo['id']);
			unset($expenseInfo['deleted']);
			unset($expenseInfo['unique_id']);
			unset($expenseInfo['unique_id_old']);
			Expense::insert($expenseInfo);
			sql_commit_transaction();
		}
		switch($action)
		{
			case 'save':
			case 'cancel':
			case 'default':
			default:
				$this->zoneRedirect("list");
				break;
		}
	}
	
	function pageStoreCategories($p)
	{
		$year = $this->getZoneParam('year');
		$month = $this->getZoneParam('month');
		$user = getLoggedInUser();
		$storeCategories = sql_fetch_map("select 
				store_category.id, category_id, store, category.color
			from 
				store_category 
				left outer join category on store_category.category_id = category.id 
			where 
				store_category.owner_id = {$user->getId()}
			order by
				store_category.list_order", "id");
		$categories = Category::getCategoryOptions($user->getId(), $month, $year);
		$this->guiAssign('categoryOptions', $categories);
		$this->guiAssign('storeCategories', $storeCategories);
		$this->guiAssign('year', $year);
		$this->guiAssign('month', $month);
		$this->guiDisplay('storeCategories.tpl');
	}
	
	function postStoreCategories($p)
	{
		$user = getLoggedInUser();
		$action = getPostText('actionField');
		
		if($action == 'moveUp' || $action == 'moveDown')
		{
			
			$movedId = getPostInt('storeCategoryId');
			$newPosition = getPostInt('newPosition');
			$positions = getPostInt('position');
			//unset($positions['new']);
			foreach($positions as $id => $position)
			{
				if($id == $movedId)
					sql_query("update store_category set list_order = $newPosition where id = {$id}");
				else if($position == $newPosition)
					sql_query("update store_category set list_order = $newPosition " . (($action == 'moveUp') ? ' + 1 ' : ' - 1 ') . "where id = {$id}");
				else
					sql_query("update store_category set list_order = $position where id = {$id}");
			}
			redirect(VIRTUAL_URL);
		}
		if($action == 'add')
		{
			$stores = getPostText('store');
			$store = sql_escape_string($stores['new']);
			unset($stores['new']);
			$categoryIds = getPostText('storecategory');
			$categoryId = $categoryIds['new'];
			unset($categoryIds['new']);
			sql_begin_transaction();
			sql_query("update store_category set list_order = list_order + 1 where owner_id = {$user->getId()}");
			sql_query("insert into store_category (store, category_id, owner_id) values($store, $categoryId, {$user->getId()})");
			sql_commit_transaction();
			redirect(VIRTUAL_URL);
		}
		if($action == 'changeCategory')
		{
			$changedId = getPostInt('storeCategoryId');
			$categoryIds = getPostText('storecategory');
			$categoryId = $categoryIds[$changedId];
			sql_query("update store_category set category_id = $categoryId where id = {$changedId}");
			redirect(VIRTUAL_URL);
		}
		if($action == 'changeStore')
		{
			var_dump(getRawPost());
			$changedId = getPostInt('storeCategoryId');
			$storeNames = getPostText('store');
			$storeName = $storeNames[$changedId];
			sql_query("update store_category set store = '$storeName' where id = {$changedId}");
			redirect(VIRTUAL_URL);
		}
		if($action == 'apply')
		{
			$expenses = sql_fetch_simple_map("select expense.id, expense.store 
				from 
					expense 
					inner join account 
					on 
						expense.entered_by = account.id 
				where 
					account.deleted = 0
					and expense.deleted = 0
					and category_id is null 
					and account.owner_id = {$user->getId()}", 'id', 'store');
			foreach($expenses as $id => $store)
			{
				$expense = &new Expense($id);
				$expense->applyStoreCategoryMatches($store, $user->getId(), $expense);
			}
			$this->zoneRedirect('list');
		}
	}
	
	function pageTestFlow($params)
	{
		dump_r(expense::getCashFlow() );
	}
}

