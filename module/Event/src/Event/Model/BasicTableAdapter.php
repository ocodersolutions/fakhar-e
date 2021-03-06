<?php

namespace Event\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\ResultSet\ResultSet;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class BasicTableAdapter implements ServiceLocatorAwareInterface
{
    protected $tableGateway;
    protected $oServiceLocator;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll()
    {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }

    public function getById($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function getByUnique($propName,$propValue, $showError=true)
    {
        $rowset = $this->tableGateway->select(array($propName=>$propValue));
        $row = $rowset->current();
        if (!$row) {
        	if($showError==false) return false;
            else throw new \Exception("Could not find row $propName = [${propValue}]");
        }
        return $row;
    }

    public function getTableGateway(){
        return $this->tableGateway;
    }
    
    public function getTable(){
        return $this->tableGateway->getTable();
    }
    public function getDBAdapter(){
        return $this->tableGateway->adapter;
    }
    
    public function setServiceLocator(ServiceLocatorInterface $oServiceLocator ){
    	$this->oServiceLocator = $oServiceLocator;
    	return $this;
    }    
    
    public function getServiceLocator(){
        if ( !isset($this->oServiceLocator) ) {
            throw new \Exception("Cannot Find Service Locator");
        }
        return $this->oServiceLocator;
    }
    
    protected function wrapResultByResultSet($result){
        $rs = new ResultSet();
        $rs->initialize($result);
        return $rs;
    }
    
    protected function convertArrayToInListString($array){
        return "'".implode("','",$array)."'";
    }
    protected function convertArrayToInList($array){
        return implode(",",$array);
    }
}