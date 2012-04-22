<?php

class CsvExpenseParser
{
	function CsvExpenseParser()
	{
		//do nothing
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
		if(!empty($data[0]))
		{
			//get account details, then get the next transaction.
			$this->readAccount($data);
			return $this->getTransaction();
		}
		if(empty($data[1]) || $data[1] == 'Posted Date')
		{
			//skip empty rows
			return $this->getTransaction();
		}
		$answer['store'] = $data[2];
		$i = 3;
		while(!is_numeric($data[$i]) && $i < 11)
		{
			$answer['store'] .= "," . $data[$i];//sometimes the store has a comma in the name. that's dumb, because they don't quote the fields, but oh well.
			$i++;
		}
		$answer['store'] = explode(' ' , $answer['store']);
		if(is_numeric($answer['store'][0]{0}))//when you have meaningless strings of numbers as the first item.
			array_shift($answer['store']);
		if($answer['store'][0] == 'P.O.S.')
		{
			array_shift($answer['store']);
			array_shift($answer['store']);
			array_shift($answer['store']);
		}
		$answer['store'] = trim(implode(' ' , $answer['store']));
		$answer['amount'] = $data[$i];
		//$answer['unique_id'] = $data[$i+3];
		$i = 8;
		$transactionCodes = array('618', '751', '631', '222', '219', '221', '352', '633', '890', '450', '610', '347', '521', '522', '220', '671', '880', '750', '605', '609', '624');
		while(!in_array($data[$i], $transactionCodes) && $i < 11)//same as above...
			$i++;
		$answer['date'] = substr($data[$i + 5], 0, 10);
		$answer['entered_by'] = $this->enteredBy;
		$answer['entered_byId'] = $this->enteredById;
		if($data[$i + 4] == 'Credit')
			$answer['credit'] = 1;//'-' . $answer['amount'];
		else
			$answer['credit'] = 0;
		//$answer['unique_id'] = md5($answer['store'] . $answer['amount'] . $answer['date'] . $answer['credit'] . $answer['entered_by']);
		//$answer['unique'] = $answer['store'] . $answer['amount'] . $answer['date'] . $answer['credit'] . $answer['entered_by'];
		return $answer;
	}

	function readAccount($data)
	{
		$this->accountType = $data[0];
		$this->enteredBy = $data[1];
		$this->enteredById = '';
		$this->account = $data[2];
		$this->balance = $data[4];
		$this->routing = $data[6];
	}
}
