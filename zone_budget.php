<?php

class zone_budget extends zone_sequence
{
	function zone_budget()
	{
		
	}
	
	function initZone($params)
	{
		
	}
	
	function initPages($params)
	{
		$this->guiAssign('zoneUrl', $this->getZoneUrl());
	}
	
	function pageDefault($params)
	{
		$month = $params[2];
		$year = $params[1];
		$categories = Category::getBudgetList($month, $year);
		
		$this->guiAssign('categories', $categories);
		$this->guiAssign('month', $month);
		$this->guiAssign('prevmonth', $month - 1);
		$this->guiAssign('nextmonth', $month + 1);
		$this->guiAssign('year', $year);
		$this->guiAssign('prevyear', $year - 1);
		$this->guiAssign('nextyear', $year + 1);
		$this->guiDisplay('default.tpl');
	}
	
	function postDefault($params)
	{
		$month = $params[2];
		$year = $params[1];
		$action = getPostText('actionField');
		switch($action)
		{
			case 'editCat':
				$catId = getPostInt('catId');
				$this->zoneRedirect("editCat/$catId/$year/$month");
			case 'addCat':
				$this->zoneRedirect("editCat/new/$year/$month");
			case 'viewExpenses':
				BaseRedirect("/expenses/list/$year/$month");
			default:
				echo_r($action);
				break;
		}
	}
	
	function pageEditCat($params)
	{
		global $strings;
		$catId = $params[1];
		$year = $params[2];
		$month = $params[3];
		$user = getLoggedInUser();
		if($catId == 'new')
		{
			$categoryInfo = Category::getBlankInfo($user->getId());
		}
		else
		{
			$category = &new Category($catId);
			$categoryInfo = $category->getInfo($month, $year);
		}
		$name = &getGuiControl('text', 'name');
		$name->setParam('validate', array('required' => true));
		$name->setValue($categoryInfo['name']);
		$this->guiAssign('name', $name);
		$amount = &getGuiControl('text', 'amount');
		$amount->setParam('validate', array('type' => 'numeric'));
		$amount->setValue(abs($categoryInfo['amount']));
		$this->guiAssign('amount', $amount);
		$fund = &getGuiControl('radio', 'fund');
		$fund->setParam('index', array(0 => 'budget', 1 =>'envelope'));
		$fund->setValue($categoryInfo['fund'] > 0 ? 1 : 0);
		$this->guiAssign('fund', $fund);
		$income = &getGuiControl('radio', 'income');
		$income->setParam('index', array(0 => 'outgoing', 1 =>'income'));
		$income->setValue($categoryInfo['amount'] > 0 ? 1 : 0);
		$this->guiAssign('income', $income);
		$unused = Category::getUnusedColors($user->getId());
		foreach($strings['colors'] as $color => $value)
		{
			if(isset($unused[$color]))
				$colors[$color] = $color . ' (unused)';
			else
				$colors[$color] = $color;
		}
		
		$color = &getGuiControl('select', 'color');
		$color->setParam('index', $colors);
		$color->setParam('optionclass', '%s');
		$color->setValue($categoryInfo['color']);
		$this->guiAssign('colors', $colors);
		$this->guiAssign('color', $color);
		$description = &getGuiControl('text', 'description');
		$description->setValue($categoryInfo['description']);
		$this->guiAssign('description', $description);
		
		$comments = &getGuiControl('text', 'comments');
		$comments->setValue($categoryInfo['comments']);
		$this->guiAssign('comments', $comments);
		
		$this->guiAssign('month', $month);
		$this->guiAssign('year', $year);
		$this->guiDisplay('editCat.tpl');
	}
	
	function postEditCat($params)
	{
		$catId = $params[1];
		$year = $params[2];
		$month = $params[3];
		$user = getLoggedInUser();
		$info['name'] = getPostText('name');
		$info['amount'] = getPostText('amount');
		if(getPostInt('income') == 1)
			$info['amount'] *= -1;
		$info['fund'] = getPostInt('fund');
		$info['color'] = getPostText('color');
		$info['description'] = getPostText('description');
		$info['comments'] = getPostText('comments');
		$info['owner_id'] = $user->getId();
		$action = getPostText('actionField');
		if($action == 'save')
		{
			if($catId == 'new')
			{
				$category = &Category::insert($info, $month, $year);
			}
			else
			{
				$category = &new Category($catId);
				$category->setInfo($info, $month, $year);
			}
			BaseRedirect("/expenses/$year/$month/list");
		}
		else
		{
			BaseRedirect("/expenses/$year/$month/list");
		}
	}
	
	function pageDeleteCat($params)
	{
		$catId = $params[1];
		$year = $params[2];
		$month = $params[3];
		Category::delete($catId, $month, $year);
		BaseRedirect("/expenses/$year/$month/list");
		//$this->zoneRedirect("$year/$month");
	}
	
	function pagePrintPie($params)
	{
		global $strings;
		$user = getLoggedInUser();
		$year = $params[1];
		$month= $params[2];
		$budgetList = Category::getBudgetList($user->getId(), $month, $year);
		$pdf = &new SmartPdf(1,'wide');
		$pdf->addDivParser(new ChartParser());
		$pdf->addParser(new ChartObjectParser());
		$pdf->assign('year', $year);
		$pdf->assign('month', $month);
		$pdf->assign('categories', $budgetList);
		$pdf->assign('strings', $strings);
		$pdf->display('budget/pie.tpl');
	}
	
	function pagePrintBarChart($params)
	{
		global $strings;
		$year = $params[1];
		$month= $params[2];
		$user = getLoggedInUser();
		$budgetList = Category::getBudgetList($user->getId(), $month, $year);
		$pdf = &new SmartPdf(1, 'wide');
		$pdf->addDivParser(new ChartParser());
		$pdf->addParser(new ChartObjectParser());
		$pdf->assign('year', $year);
		$pdf->assign('month', $month);
		$pdf->assign('categories', $budgetList);
		$pdf->assign('strings', $strings);
		$pdf->display('budget/bar.tpl');
	}
	
	function pageViewLineChart($params)
	{
		global $strings;
		$startYear = $params[1];
		$startMonth = $params[2];
		if(isset($params[3]))
			$endYear = $params[3];
		else
			$endYear = $params[1];
		if(isset($params[4]))
			$endMonth = $params[4];
		else
			$endMonth = $params[2];
		$this->guiAssign('startYear', $startYear);
		$this->guiAssign('startMonth', $startMonth);
		$this->guiAssign('endYear', $endYear);
		$this->guiAssign('endMonth', $endMonth);
		$this->guiDisplay('viewLine.tpl');
	}
	
	function getPrevPeriod($month, $year)
	{
		$prevmonth = $month -1;
		$prevyear = $year;
		if($prevmonth == 0)
		{
			$prevmonth = 12;
			$prevyear = $year - 1;
		}
		return array('month' => $prevmonth, 'year' => $prevyear);
	}
	
	function getNextPeriod($month, $year)
	{
		$nextmonth = $month + 1;
		$nextyear = $year;
		if($nextmonth == 13)
		{
			$nextmonth = 1;
			$nextyear = $year + 1;
		}
		return array('month' => $nextmonth, 'year' => $nextyear);
	}
	
	function postViewLineChart($params)
	{
		$startYear = $params[1];
		$startMonth = $params[2];
		if(isset($params[3]))
			$endYear = $params[3];
		else
			$endYear = $params[1];
		if(isset($params[4]))
			$endMonth = $params[4];
		else
			$endMonth = $params[2];
		$action = getPostText('actionField');
		if($action == 'prevStart')
		{
			$start = $this->getPrevPeriod($startMonth, $startYear);
			$this->zoneRedirect("viewLineChart/{$start['year']}/{$start['month']}/{$endYear}/{$endMonth}");
		}
		if($action == 'nextStart')
		{
			$start = $this->getNextPeriod($startMonth, $startYear);
			$this->zoneRedirect("viewLineChart/{$start['year']}/{$start['month']}/{$endYear}/{$endMonth}");
		}
		if($action == 'prevEnd')
		{
			$end = $this->getPrevPeriod($endMonth, $endYear);
			$this->zoneRedirect("viewLineChart/{$startYear}/{$startMonth}/{$end['year']}/{$end['month']}");
		}
		if($action == 'nextEnd')
		{
			$end = $this->getNextPeriod($endMonth, $endYear);
			$this->zoneRedirect("viewLineChart/{$startYear}/{$startMonth}/{$end['year']}/{$end['month']}");
		}
	}
	
	function pageImageLineChart($params)
	{
		global $strings;
		$startYear = $params[1];
		$startMonth = $params[2];
		if(isset($params[3]))
			$endYear = $params[3];
		else
			$endYear = $params[1];
		if(isset($params[4]))
			$endMonth = $params[4];
		else
			$endMonth = $params[2];
		$user = getLoggedInUser();
		$endday = Category::getEndDay($endMonth, $endYear);
		$balances = Expense::getCashFlow($user->getId(), "$startYear-$startMonth-01", "$endYear-$endMonth-$endday");
		$accountNames = account::getAccounts($user->getId()) + array('Total' => array('id' => 'total', 'name' => 'Total'));
		$color = reset($strings['colors']);
		foreach($accountNames as $id => $name)
		{
			$accountNames[$id]['color'] = $color['chart'];
			$color = next($strings['colors']);
		}
		$pdf = &new SmartImage(array('width' => 750, 'height' => 480));
		$pdf->addDivParser(new ChartParser());
		$pdf->addParser(new ChartObjectParser());
		$pdf->assign('startYear', $startYear);
		$pdf->assign('startMonth', $startMonth);
		$pdf->assign('endYear', $endYear);
		$pdf->assign('endMonth', $endMonth);
		$pdf->assign('accounts', $balances);
		$pdf->assign('accountNames', $accountNames);
		$pdf->assign('strings', $strings);
		$pdf->display('budget/line.tpl');
	}
	
	function pagePrintLineChart($params)
	{
		global $strings;
		$startYear = $params[1];
		$startMonth = $params[2];
		if(isset($params[3]))
			$endYear = $params[3];
		else
			$endYear = $params[1];
		if(isset($params[4]))
			$endMonth = $params[4];
		else
			$endMonth = $params[2];
		$user = getLoggedInUser();
		$endday = Category::getEndDay($endMonth, $endYear);
		$balances = Expense::getCashFlow($user->getId(), "$startYear-$startMonth-01", "$endYear-$endMonth-$endday");
		$accountNames = account::getAccounts($user->getId()) + array('Total' => array('id' => 'total', 'name' => 'Total'));
		$color = reset($strings['colors']);
		foreach($accountNames as $id => $name)
		{
			$accountNames[$id]['color'] = $color['chart'];
			$color = next($strings['colors']);
		}
		$pdf = &new SmartPdf(1, 'wide');
		$pdf->addDivParser(new ChartParser());
		$pdf->addParser(new ChartObjectParser());
		$pdf->assign('startYear', $startYear);
		$pdf->assign('startMonth', $startMonth);
		$pdf->assign('endYear', $endYear);
		$pdf->assign('endMonth', $endMonth);
		$pdf->assign('accounts', $balances);
		$pdf->assign('accountNames', $accountNames);
		$pdf->assign('strings', $strings);
		$pdf->display('budget/line.tpl');
	}
	
	function pageTestChart($p)
	{
		$gui = &new SmartPdf(1, 'wide');
		$gui->addDivParser(new ChartParser());
		$gui->addParser(new ChartObjectParser());
		$gui->assign('data', array(
					1 => array(
						'color' => '#FF0000',
						'name' => 'Rent',
						'amount' => 1200,
						'spent' => 1200),
					2 => array(
						'color' => '#00FF00',
						'name' => 'Food',
						'amount' => 400,
						'spent' => 353.29),
					3 => array(
						'color' => '#0000FF',
						'name' => 'Utilities',
						'amount' => 200,
						'spent' => 239.22)
					));
		if(!isset($p[1]) || $p[1] == 'pie')
			$gui->display('budget/testChart.tpl');
		else
			$gui->display('budget/testBarChart.tpl');
	}
	
	function pageTestGuiControl($p)
	{
		global $gui;
		$select = &getGuiControl('select', 'select');
		$select->setParam('index', array('option1' => 'option1', 'option2' => 'option2'));
		$gui->assign("select", $select);
		$gui->display('testGuiControl.tpl');
	}
}
