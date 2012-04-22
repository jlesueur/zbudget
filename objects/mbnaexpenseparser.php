<?php

class MBNAExpenseParser
{
	function MBNAExpenseParser($account)
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
		if(empty($data[0]) || $data[0] == 'Posted Date')
		{
			//skip empty rows
			return $this->getTransaction();
		}
		$answer['store'] = $data[2];
		$answer['amount'] = $data[4];
		if($answer['amount']{0} == '-')
		{
			$answer['amount'] = substr($answer['amount'], 1);
			$answer['credit'] = 0;
		}
		else
			$answer['credit'] = 1;
		$answer['date'] = $data[0];
		$answer['entered_by'] = $this->enteredBy;
		return $answer;
	}
}

