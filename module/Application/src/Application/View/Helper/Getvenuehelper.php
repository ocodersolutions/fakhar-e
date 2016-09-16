<?php
namespace Application\View\Helper;
use Zend\View\Helper\AbstractHelper;
 
class Getvenuehelper extends AbstractHelper
{
	protected $stringSearch;

    protected $falsify;
    public function __invoke($stringSearch, $falsify)
    {
        $falsify = Round($stringSearch.length() * 0.3);
	}
	 public function compareResult($result, $stringSearch)
    {
        if (($result.length()<($stringSearch - $falsify) || ($result.length()>($stringSearch + $falsify))
        {
            return false;
            $i=$j=$err=0;
            while ($i < ($stringSearch - $falsify) && $j < ($stringSearch - $falsify))
            {
            	if ($stringSearch[i] != $result[j])
            	{
            		$err += 1;
            		for ($k = 0; $k <= $falsify; $k++)
            		{
            			if (($i + $k < $stringSearch.length()) && $stringSearch[$i + $k] == $result[$j])
	                    {
	                        $i += $k;
	                        break;
	                    }
	                    else if (($j + $k < $result.length()) && $stringSearch[$i] == $result[$j + $k])
	                    {
	                        $j += $k;
	                        break;
	                    }
            		}
            	}
            	$i++;
            	$j++;
            }
            $err +=  $stringSearch.length() - i + $result.length() - j;
        	if ($err <= $falsify)
        	{
        		return true;
        	}else{
        		return false;
        	} 
    	}	
	}
}