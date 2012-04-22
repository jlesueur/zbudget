<?php

class WFExpenseParser
{
	function WfExpenseParser()
	{
		//do nothing
		//$this->accountType = 
		//$this->enteredBy = $account->getName();
		//$this->account = $data[2];
		//$this->balance = $data[4];
		//$this->routing = $data[6];
	}

	function open($filename)
	{
		$this->file = fopen($filename, 'r');
	}

	function &getTransaction()
	{
		$data = fgetcsv($this->file, 1000, ",");
		if($data === false)
		{
			$answer = false;
			return $answer;
		}
		if(empty($data[0]) || $data[0] == 'Category')
		{
			//skip empty rows
			return $this->getTransaction();
		}
		$answer['store'] = $data[3];
		$answer['store'] = explode(' ' , $answer['store']);
		if(is_numeric($answer['store'][0]{0}))//when you have meaningless strings of numbers as the first item.
			array_shift($answer['store']);
		if($answer['store'][0] == 'CHECK' && $answer['store'][1] == 'CRD')
		{
			array_shift($answer['store']);
			array_shift($answer['store']);
			array_shift($answer['store']);
			array_shift($answer['store']);
		}
		if($answer['store'][0] == 'POS' )
		{
			array_shift($answer['store']);
			array_shift($answer['store']);
			array_shift($answer['store']);
			array_shift($answer['store']);
		}
		$answer['store'] = trim(implode(' ' , $answer['store']));
		$answer['amount'] = str_replace(',', '' , substr($data[5], 1));
		$answer['date'] = $data[1];
		$answer['entered_by'] = $data[4];
		//if($data[$i + 4] == 'Credit')
		//	$answer['credit'] = 1;//'-' . $answer['amount'];
		//else
			$answer['credit'] = 0;
		return $answer;
	}
}
