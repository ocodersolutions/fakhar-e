<?php
namespace Application\View\Helper;
use Zend\View\Helper\AbstractHelper;
 
class Stringhelper extends AbstractHelper
{
    public function __invoke($string)
    {
        $max_length = 25;

        if(strlen($string) > $max_length) {
            $offset = ($max_length - 3) - strlen($string);
            return substr($string, 0, strrpos($string, ' ', $offset)) . '...';
        } else {
            return $string;
        }

    }
}