<?php
namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;

class VenueTable extends BasicTableAdapter {
	protected $table = 'Venue';
	public function insert(){
		
	}
	public function delete(){
	
	}
	public function viewlist($string){
        $sql = new Sql($this->getServiceLocator()->get('db'));
        $select = $sql->select(array('v' => 'Venue'));
        $where = new \Zend\Db\Sql\Where();
        $where -> like('v.title', '%'.$string.'%');
        $select->where($where);
        $resultSet = array();
        $results = $sql->prepareStatementForSqlObject($select)->execute();
        $resultSet = new \Zend\Db\ResultSet\ResultSet();
        $resultSet->initialize($results);
        $resultSet = $resultSet->toArray();

        return $resultSet;
	}
  	public function getAllVenue($isActive){
        $sql = new Sql($this->getServiceLocator()->get('db'));
        $select = $sql->select(array('v' => 'Venue'));
        $where = new \Zend\Db\Sql\Where();
        $select->where(array('v.isActive'=>$isActive));
        $resultSet = array();
        $results = $sql->prepareStatementForSqlObject($select)->execute();
        $resultSet = new \Zend\Db\ResultSet\ResultSet();
        $resultSet->initialize($results);
        $resultSet = $resultSet->toArray();

        return $resultSet;
	}
    public function getVenueStyle($id){
        $sql = new Sql($this->getServiceLocator()->get('db'));
        $select = $sql->select(array('s' => 'venuestyle'));
        $where = new \Zend\Db\Sql\Where();
        $select->where(array('s.venue_id'=>$id));
        $resultSet = array();
        $results = $sql->prepareStatementForSqlObject($select)->execute();
        $resultSet = new \Zend\Db\ResultSet\ResultSet();
        $resultSet->initialize($results);
        $resultSet = $resultSet->toArray();

        return $resultSet;
    }
	
}
