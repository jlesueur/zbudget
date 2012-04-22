<?php

class OfxExpenseParser
{
	function OfxExpenseParser()
	{
		//do nothing
	}

	function open($filename)
	{
		$this->file = fopen($filename, 'r');
		while(($string = trim(fgets($this->file))) !== '<OFX>' && !feof($this->file))
			;//do nothing;
		while(($string = trim(fgets($this->file))) !== '</SIGNONMSGSRSV1>' && !feof($this->file))
			;//do nothing;
		
	}
	
	function findNextTransaction()
	{
		while(!in_array(($string = trim(fgets($this->file))), array('<CCACCTFROM>', '<BANKACCTFROM>', '<STMTTRN>')) && !feof($this->file))
			;//do nothing;
		if($string == '<BANKACCTFROM>')
		{
			while(($string = trim(fgets($this->file))) !== '</BANKACCTFROM>')
			{
				$begin = strpos($string, '<') + 1;
				$end = strpos($string, '>');
				$label = substr($string, $begin, $end - $begin);
				if($label == 'ACCTID')
					$this->enteredById = substr($string, $end+1);
			}
			/*
			$routing = trim(fgets($this->file));//<BANKID>
			$accountId = trim(fgets($this->file));//<ACCTID>
			$accountType = trim(fgets($this->file));//<ACCTTYPE>
			assert(substr($routing, 0, 8) == '<BANKID>');
			assert(substr($routing, 0, 8) == '<ACCTID>');
			assert(substr($routing, 0, 10) == '<ACCTTYPE>');
			$this->accountType = substr($accountType, 10);
			$this->enteredById = substr($accountId, 8);
			$this->enteredBy = '';
			fgets($this->file);//</BANKACCTFROM>
			*/
			$this->enteredBy = '';
			//var_dump($this->enteredById);
			$this->findNextTransaction();
		}
		else if($string == '<CCACCTFROM>')
		{
			while(($string = trim(fgets($this->file))) !== '</CCACCTFROM>')
			{
				$begin = strpos($string, '<') + 1;
				$end = strpos($string, '>');
				$label = substr($string, $begin, $end - $begin);
				if($label == 'ACCTID')
					$this->enteredById = substr($string, $end+1);
			}
			/*
			$accountId = trim(fgets($this->file));//<ACCTID>
			assert(substr($routing, 0, 8) == '<ACCTID>');
			$this->enteredById = substr($accountId, 8);
			*/
			$this->enteredBy = '';
			//var_dump($this->enteredById);
			$this->findNextTransaction();
		}
		if(feof($this->file))
			return false;
		return true;
	}
	
	function handleLabel($label, $value, &$answer)
	{
		switch($label)
		{
		case 'TRNTYPE':
			if($value == 'CHECK')
				$answer['store'] = 'CHECK';
			break;
		case 'CHECKNUM':
			if(isset($answer['store']))
				$answer['store'] .= ' NUM ' . $value;
			else
				$answer['store'] = 'CHECK NUM' . $value;
			break;
		case 'DTPOSTED':
			$date = $value;
			$answer['date'] = substr($date, 0, 4) . '-' . substr($date, 4, 2) . '-' . substr($date, 6, 2);
			break;
		case 'TRNAMT':
			$answer['amount'] = $value;
			break;
		case 'NAME':
			$answer['store'] = $value;
			break;
		case 'MEMO'://<MEMO> is a more complete version of <NAME> on zionsbank ofx files, but in other files is a useless transaction number...
			if(!isset($answer['store']) || strlen($value) > strlen($answer['store']))
				$answer['store'] = $value;
			break;
		default:
			break;
		}
	}

	function &getTransaction()
	{
		if(!$this->findNextTransaction())
		{
			$answer = false;
			return $answer;
		}
		$answer = array();
		while(strpos(($string = trim(fgets($this->file))), '</STMTTRN>') === false )
		{
			//var_dump($string);
			$begin = strpos($string, '<') + 1;
			$end = strpos($string, '>');
			$label = substr($string, $begin, $end - $begin);
			$value = substr($string, $end+1);
			//var_dump($label);
			//var_dump($value);
			$this->handleLabel($label, $value, $answer);
		}
		if(strpos($string, '</STMTTRN>') !== 0)
		{
			$begin = strpos($string, '<') + 1;
			$end = strpos($string, '>');
			$label = substr($string, $begin, $end - $begin);
			$value = substr($string, $end+1, strpos($string, '</STMTTRN>') - ($end+1));
			$this->handleLabel($label, $value, $answer);
		}
		//die();
		/*
		$credit = trim(fgets($this->file));//<TRNTYPE>
		assert(substr($credit, 0, 9) == '<TRNTYPE>');
		$answer['credit'] = substr($credit, 9) !== 'DEBIT';
		$date = trim(fgets($this->file));//<DTPOSTED>
		assert(substr($date, 0, 10) == '<DTPOSTED>');
		$date = substr($date, 10);
		$answer['date'] = substr($date, 0, 4) . '-' . substr($date, 4, 2) . '-' . substr($date, 6, 2);
		$amount = trim(fgets($this->file));//<TRNAMT>
		assert(substr($amount, 0, 8) == '<TRNAMT>');
		$answer['amount'] = substr($amount, 8);
		*/
		/*
		$fitid = trim(fgets($this->file));//<FITID>
		assert(substr($fitid, 0, 7) == '<FITID>');
		$name = trim(fgets($this->file));//<NAME>
		assert(substr($name, 0, 6) == '<NAME>');
		$memo = trim(fgets($this->file));//<MEMO>
		assert(substr($memo, 0, 6) == '<MEMO>');
		$answer['store'] = substr($memo, 6);
		*/
		if($answer['amount'][0] == '-')
		{
			$answer['credit'] = 0;
			$answer['amount'] = substr($answer['amount'], 1);
		}
		else
		{
			$answer['credit'] = 1;
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
		$answer['entered_by'] = $this->enteredBy;
		$answer['entered_byId'] = $this->enteredById;
		
		return $answer;
	}

	/*
	function readAccount($data)
	{
		$this->accountType = $data[0];
		$this->enteredBy = $data[1];
		$this->account = $data[2];
		$this->balance = $data[4];
		$this->routing = $data[6];
	}
	*/
}
