<?php
namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;

class AttributeListTable extends BasicTableAdapter {

	public function getAttributeName(){
                $sql = new Sql($this->getServiceLocator()->get('db'));
                $select = $sql->select(array('sl' => 'AttributesList'));
                $resultSet = array();
                $results = $sql->prepareStatementForSqlObject($select)->execute();
                $resultSet = new \Zend\Db\ResultSet\ResultSet();
                $resultSet->initialize($results);
                $resultSet = $resultSet->toArray();
                return $resultSet;

        
	}
	public function getAttributeValue($value){
                $sql = new Sql($this->getServiceLocator()->get('db'));
                $select = $sql->select(array('sl' => 'AttributesList'));
                $select->where(array('sl.attribute_name' => $value));
                $resultSet = array();
                $results = $sql->prepareStatementForSqlObject($select)->execute();
                $resultSet = new \Zend\Db\ResultSet\ResultSet();
                $resultSet->initialize($results);
                $resultSet = $resultSet->toArray();     
                return $resultSet;
	}
  
	
}
	