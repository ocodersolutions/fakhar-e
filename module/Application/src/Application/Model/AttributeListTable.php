<?php
namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;

class AttributeListTable extends BasicTableAdapter {

	public function getAttributeName(){
       	$userId = 1;
        $sql = new Sql($this->getServiceLocator()->get('db'));
        $select = $sql->select(array('al' => ''));
        $select->where(array('al.attribute_name' => $userId));

        $resultSet = array();
        $results = $sql->prepareStatementForSqlObject($select)->execute();
        $resultSet = new \Zend\Db\ResultSet\ResultSet();
        $resultSet->initialize($results);
        $resultSet = $resultSet->toArray();
        echo '<pre>';
        var_dump($resultSet ); die;
        return $resultSet;
	}
	public function getAttributeValue(){
       
	}
  
	
}
	