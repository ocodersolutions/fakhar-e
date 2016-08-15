<?php

namespace Event\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class EventsLog 
{
    public $id;
    public $user_ip;
    public $user_id;
    public $page;
    public $product_selected;
    public $updated;
    public $created;
    
    public function exchangeArray($data)
    {
        $this->id 			= (isset($data['id'])) ? $data['id'] : null;
        $this->user_ip 		= (isset($data['user_ip'])) ? $data['user_ip'] : null;
        $this->user_id 	= (isset($data['user_id'])) ? $data['user_id'] : null;
        $this->page 	= (isset($data['page'])) ? $data['page'] : null;
        $this->product_selected 		= (isset($data['product_selected'])) ? $data['product_selected'] : null;
        $this->updated	= (isset($data['updated'])) ? $data['updated'] : null;
        $this->created		= (isset($data['created'])) ? $data['created'] : null;
    }
    
}
