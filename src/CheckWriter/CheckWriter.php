<?php

namespace CheckWriter;


class CheckWriter
{
	public $amount;
	public $description;

    public function __construct($amount)
    {
    	$this->amount = $amount;
    }

    public function getDescription()
    {
    	$this->description = $this->convertToCheckFormat($this->amount);
        return $this->description;
    }

    public function convertToCheckFormat()
    {
    	//make sure amount string is numberic
    	if(!is_numeric($this->amount))
    	{
    		throw new \Exception("Check Amount is invalid");
    	}

    	//remove any commas
    	$this->amount = str_replace(",", "", $this->amount);
    
    	//convert to decimal format
    	$this->amount = number_format($this->amount, 2, ".", "");	   	
    	
    	//split dollars and cents to process separately
    	$amountarray = explode(".", $this->amount);

    	//convert dollars and cents amounts to text
    	$cents =$this->convertCentsToText($amountarray[1]);
    	$dollars = $this->convertDollarToText($amountarray[0]);

    	//return formatted text in check format with first number capitalized
    	return ucfirst($dollars) . ' and ' . $cents . ' dollars';

    }

    public function convertCentsToText($cents)
    {
    	//if zero cents convert to 'no'
    	if($cents == "00")
    	{
    		$cents = "no";
    	}
    	//add out of 100
    	$centsText = $cents . '/100';

    	//return formatted
    	return $centsText;
    }

    public function convertDollarToText($dollars)
    {

    	
    	//convert to text version using NumberFormatter Class if class exists
    	if(class_exists("\NumberFormatter")) 
    	{
    		$numberFormatter = new \NumberFormatter("en", \NumberFormatter::SPELLOUT);
    		$dollarText = $numberFormatter->format($dollars);
    	}
    	//use local numbertoText function if intl extension not loaded in environment.
    	else 
    	{
    		$dollarText = $this->numbertoText($dollars);
    	}

    	//return dollar amount
    	return $dollarText;
    }

	//this function can only handle numbers up to a trillion
    public function numbertoText($number) 
    {
    	if($number > '999999999999')
    	{
    		throw new \Exception("Check amount out of range");
    	}
    	//conversion array
    	$numberwords = array(
					0 =>"zero", 
					1 => "one", 
					2 => "two", 
					3 => "three", 
					4 => "four", 
					5 => "five", 
					6 => "six", 
					7 => "seven", 
					8 => "eight", 
					9 => "nine", 
					10 => "ten", 
					11 => "eleven", 
					12 => "twleve", 
					13 => "thirteen", 
					14 => "fourteen", 
					15 => "fifteen", 
					16 => "sixteen", 
					17 => "seventeen", 
					18 => "eightteen", 
					19 => "nineteen",
					20 => "twenty",
					30 => "thirty",
					40 => "fourty",
					50 => "fifty",
					60 => "sixty",
					70 => "seventy",
					80 => "eighty",
					90 => "ninety"
					); 

    	//convert number to array of digits and reverse order
    	$numberarray = array_reverse(str_split($number));

    	//initialize word array
    	$wordarray = array();

    	//loop through digit array
    	foreach($numberarray as $x=>$digit)
    	{
    		
    		if ($x == 0 || $x%3 == 0)
    		{   			
    			$numberWord = '';
    			//if there is a digit before the ones place
    			if(isset($numberarray[$x+1])) {
    				//tens place has a zero
    				if($numberarray[$x+1] == 0)
    				{
    					$numberWord =  $numberwords[$digit];
    				}
    				//tens place has a one use *teen word
    				elseif($numberarray[$x+1] == 1)
    				{
    					$teen = '1' . $digit;
    					$numberWord =  $numberwords[$teen];
    				}
    				//get tens place value 
    				else
    				{
    					$tens = $numberarray[$x+1] . '0';
    					//if ones place is zero use tens word directory, otherwise hypenate
    					if($digit==0)
    					{
    						$tensformat = $numberwords[$tens];
    					}
    					else
    					{
    						$tensformat = $numberwords[$tens] . '-' . $numberwords[$digit];
    					}
    					$numberWord = $tensformat;
    				}
    			}
    			// no tens - single digit
    			else {
    				$numberWord = $numberwords[$digit];
    			}


    			//add  thousand, million billion based on array position
    			switch($x) {
    				case 3:
    					$otherplace = ' thousand';
    					break;
    				case 6:
    					$otherplace = ' million';
    					break;
    				case 9:
    					$otherplace = ' billion';
    					break;
    				default:
    					$otherplace = '';
    			}
    			if($numberWord != '')
    			{
    				array_unshift($wordarray, $numberWord . $otherplace);
    			}	


    		}
    		elseif($x == 2 || $x%3 == 2)
    		{
    			array_unshift($wordarray, $numberwords[$digit] . ' hundred');
    		}
    		
    	}

    	return implode(" ", $wordarray);
    }
}
