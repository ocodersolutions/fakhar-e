<?php

namespace Event\Model;

use Event\Model\BasicTableAdaptor;
use ReflectionMethod;
use Traversable;


class BasicModelAdapter 
{

    
    protected $idProperty;
    public function __construct($idProperty='id')
    {
         $this->idProperty = $idProperty;

    }

    public function exchangeArray($data){
        $this->arrayToProperties($data,$this);
    }

    protected function arrayToProperties(array $data, $object)
    {
        if (!is_object($object)) {
            throw new Exception\BadMethodCallException(sprintf(
                '%s expects the provided $object to be a PHP object)', 
                __METHOD__
            ));
        }

        foreach ($data as $property => $value) {
           $object->$property = $value;
           // if (property_exists($object, $property)) {
               // unknown properties are skipped
           //     $object->$property = $value;
           // }
        }

        return $object;
    }
    
    public function getArrayCopy( $filterCloumns=array()  )
    {
        $data = get_object_vars($this);
        
        if( is_array($filterCloumns) && count($filterCloumns) ) {
        	
        	$filterCloumns = array_flip( $filterCloumns );
	        foreach($filterCloumns as $key => $val) {
	        	if( isset($data[$key]) ) {
	        		$filterCloumns[$key] = $data[$key];
	        	}
	        }
	        return $filterCloumns;
        }
        return $data;
    }
    
    public function toArray(){
        return get_object_vars($this);
    }    

    public function getIdProperty(){
        return $this->idProperty;
     }

}
