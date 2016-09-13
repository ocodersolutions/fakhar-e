<?php
namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;

class StyleListTable extends BasicTableAdapter {
	protected $table = 'Style';
	public function insert(){
		
	}
	public function delete(){
	
	}
	public function viewlist($userId){

		$userId = 1;
        $sql = new Sql($this->getServiceLocator()->get('db'));
        $select = $sql->select(array('ac' => 'Style'));
        $select->where(array('ac.userId' => $userId));

        $resultSet = array();
        $results = $sql->prepareStatementForSqlObject($select)->execute();
        $resultSet = new \Zend\Db\ResultSet\ResultSet();
        $resultSet->initialize($results);
        $resultSet = $resultSet->toArray();

        return $resultSet;
	}
  
	
}
