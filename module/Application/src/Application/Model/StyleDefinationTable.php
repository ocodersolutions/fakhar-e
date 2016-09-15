<?php
namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;

class StyleDefinationTable extends BasicTableAdapter {
	protected $table = 'styledefination';
	protected $tableGateway;

	public function __construct(TableGateway $tableGateway){
	     $this->tableGateway = $tableGateway;
	}
	public function insert($attr, $value, $id){
    		$data = array(
            'styleId' => $id,
            'attribute' => $attr,
            'value' => $value,
        );
    	$result = $this->tableGateway->insert($data);
        return $result;
	}
	public function liststyle($id){
        $sql = new Sql($this->getServiceLocator()->get('db'));
        $select = $sql->select(array('sl' => 'styledefination'));
        $select->where(array('sl.styleId' => $id));

        $resultSet = array();
        $results = $sql->prepareStatementForSqlObject($select)->execute();
        $resultSet = new \Zend\Db\ResultSet\ResultSet();
        $resultSet->initialize($results);
        $resultSet = $resultSet->toArray();
        return $resultSet;
	}
  
	
}
	