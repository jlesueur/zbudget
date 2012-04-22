<?php

class zone_default extends zone
{

	function zone_default()
	{
	}
	
	function initZone($params)
	{
		global $strings;
		$this->guiAssign('strings', $strings);
	}
	
	function initPages($params)
	{
		//do nothing
		global $sGlobals;
		$this->guiAssign('zoneUrl', $this->getZoneUrl());
		if(isLoggedIn() && (!isset($params[0]) || $params[0] != 'logout'))
		{
			$month = (int)date('m');
			$year = date('Y');
			BaseRedirect(zone_expenses::makePath($year, $month));
		}
	}
	
	function pageDefault($params)
	{
		//intro page....
		$this->zoneRedirect('login');
		BaseRedirect(zone_register::makePath());
	}
	
	function pageLogin($p)
	{
		if(isset($p[1]))
			$this->guiAssign('error', $p[1]);
		$this->guiAssign('title', 'Login to zBudget');
		$this->guiDisplay('login.tpl');
	}
	
	function postLogin($p)
	{
		$user = user::login(getPostText('email'), getPostText('password'));
		if($user === false)
		{
			$this->zoneRedirect('login/error');
		}
		setLoggedInUser($user);
		initLoggedInUser();//if the user is initted, we fall through here...
		$month = (int)date('m');
		$year = date('Y');
		BaseRedirect(zone_expenses::makePath($year, $month));
	}
	
	function pageLogout($p)
	{
		session_destroy();
		BaseRedirect('');
	}
	
	function pageTest($params)
	{
		//$editor = &getGuiControl('editor', 'test');
		//$this->guiAssign('editor', $editor);
		//$this->guiDisplay('test.tpl');
		$gui = &new SmartPdf();
		//$gui = &new SmartImage(array('width' => 600, 'height' => 400));
		$gui->assign('test', 'Hi this is something');
		$gui->display('default/newtest.tpl');
	}
	
	function postTest($params)
	{
		dump_r(getRawPost('test'));
		dump_r(getPostText('test'));
		$html = getPostHTML('test');
		dump_r($html);
		sql_query("insert into test (text) values('$html')");
	}
	
	function pagePdfTest($params)
	{
		global $strings;
		$budgetList = Category::getBudgetList(9, 2006);
		$pdf = &new SmartPdf();
		$pdf->addDivParser(new ChartParser());
		$pdf->addParser(new ChartObjectParser());
		$pdf->assign('html', 'hello');
		$pdf->assign('categories', $budgetList);
		$pdf->assign('strings', $strings);
		$pdf->display('default/test.tpl');
	}
	
	function pageTestAutoComplete($inPath)
	{
		$options = array("listvalue");
		
		$answer = '<ul>';
		foreach($options as $option);
			$answer .= '<li>' . $option . '<li>';
		$answer .= '</ul>';
		echo $answer;
	}
}