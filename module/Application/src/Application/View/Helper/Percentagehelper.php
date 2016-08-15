<?php
namespace Application\View\Helper;
use Zend\View\Helper\AbstractHelper;
 
class Percentagehelper extends AbstractHelper
{
    public function __invoke($price, $saleprice)
    {
        
			if($price != 0.00 && $price != 0 && $saleprice != 0.00 && $saleprice != 0 && $price != $saleprice)
			{
				$discount = $price - $saleprice;
				$percentage = ($discount/$price)*100;
				return round($percentage, 0, PHP_ROUND_HALF_DOWN).'%';	
			}
			else
			{ 
				return '0%';			
			}

    }
}