<?php

namespace Application\Service;
 
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
 
use ReflectionMethod;
use ReflectionClass;
 
class CommonTableAbstractFactory implements AbstractFactoryInterface
{
    
    const TABLE_CLASS_POSTFIX='Table';
    const GENERIC_TABLE_ADAPTER='BasicTableAdapter';
    const NAMESPACE_PREFIX='Application\Model\\';
    
    public function canCreateServiceWithName(ServiceLocatorInterface $locator, $name, $requestedName)
    {
        if ($this->isModelTableClassName($requestedName) && class_exists($this::NAMESPACE_PREFIX.$this->getModelName($requestedName))){
            return true;
        }
        return false;
    }
    
    public function createServiceWithName(ServiceLocatorInterface $locator, $name, $requestedName)
    {
        try{
            $dbAdapter = $locator->get('Zend\Db\Adapter\Adapter');
            $resultSetPrototype = new ResultSet();
            $modelClassName = $this::NAMESPACE_PREFIX.$this->getModelName($requestedName);
            
            $modelObject = new $modelClassName();
             
            $resultSetPrototype->setArrayObjectPrototype($modelObject);
            //Table name is model class name in all lower case
            $tableGateway = new TableGateway($this->getModelName($requestedName), $dbAdapter, null, $resultSetPrototype);
                             
            $modelTableClassName=$this::NAMESPACE_PREFIX.$requestedName;
            if(class_exists($modelTableClassName)){
                $class = new ReflectionClass($modelTableClassName);
            }else{
                $class = new ReflectionClass($this::NAMESPACE_PREFIX.$this::GENERIC_TABLE_ADAPTER);
            }
            return $class->newInstanceArgs(array($tableGateway,$modelObject->getIdProperty()));
        }catch(Exception $e){
            print_r($e);
            throw $e;
        }
     }
    
    private function isModelTableClassName($requestedName){
        return preg_match('/'.$this::TABLE_CLASS_POSTFIX.'$/', $requestedName);
    }
    
    private function getModelName($requestedName){
        if($this->isModelTableClassName($requestedName)){
            return preg_replace('/'.$this::TABLE_CLASS_POSTFIX.'$/', "", $requestedName); 
        }
    }
}