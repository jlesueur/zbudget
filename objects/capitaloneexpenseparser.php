<?php

class CapitalOneExpenseParser
{
	function CapitalOneExpenseParser($account)
	{
		//do nothing
		//$this->accountType = 
		$this->enteredBy = $account->getName();
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
		if(empty($data[0]) || $data[0] == 'Number')
		{
			//skip empty rows
			return $this->getTransaction();
		}
		$answer['store'] = $data[2];
		$answer['amount'] = substr($data[4], 1);
		if($data[3] == 'Credit')
			$answer['credit'] = 1;
		else
			$answer['credit'] = 0;
		$answer['date'] = $data[1];
		$answer['entered_by'] = $this->enteredBy;
		return $answer;
	}
}

