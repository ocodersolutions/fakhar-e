<?php
namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;

class AttributeListTable extends BasicTableAdapter {

	public function getAttributeName(){
       	$userId = 1;
        $sql = new Sql($this->getServiceLocator()->get('db'));
        $select = $sql->select(array('al' => 'Style'));
        $select->where(array('al.userId' => $userId));

        $resultSet = array();
        $results = $sql->prepareStatementForSqlObject($select)->execute();
        $resultSet = new \Zend\Db\ResultSet\ResultSet();
        $resultSet->initialize($results);
        $resultSet = $resultSet->toArray();

        return $resultSet;
	}
	public function getAttributeValue(){
       
	}
  
	
}
	