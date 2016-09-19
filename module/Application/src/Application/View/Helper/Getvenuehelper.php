<?php
namespace Application\View\Helper;
use Zend\View\Helper\AbstractHelper;
 
class Getvenuehelper extends AbstractHelper
{
	// public function __invoke($stringSearch, $falsify)
 //    {
        
	// }
	 public function comparestring($stringSearch , $result)
    {
        $falsify = round(strlen($stringSearch) * 0.3);
        if ((strlen($result) < (strlen($stringSearch) + $falsify)) || (strlen($result)>(strlen($stringSearch) - $falsify)))
        {
            $compare ='';
            $i=$j=$err=0;
            while ($i < strlen($stringSearch) && $j < strlen($result))
            {
            	if ($stringSearch[$i] != $result[$j])
            	{
            		$err += 1;
            		for ($k = 0; $k <= $falsify; $k++)
            		{
            			if (($i + $k < strlen($stringSearch)) && $stringSearch[$i + $k] == $result[$j])
	                    {
	                        $i += $k;
	                        break;
	                    }
	                    else if (($j + $k < strlen($result)) && $stringSearch[$i] == $result[$j + $k])
	                    {
	                        $j += $k;
	                        break;
	                    }
            		}
            	}
            	$i++;
            	$j++;
            }
            $err +=  strlen($stringSearch) - $i + strlen($result) - $j;
        	if ($err <= $falsify)
        	{  
                $compare=$result;
        		return $compare;   
        	}else{
        		$compare='';
                return $compare;
        	} 
    	}

	}
     public function test($string)
        {
            $output = round(strlen($string)*0.3);
            echo $output;
        }   
}