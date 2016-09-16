<?php
namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;

class VenueTable extends BasicTableAdapter {
	protected $table = 'venue';
	public function insert(){
		
	}
	public function delete(){
	
	}
	public function viewlist($isActive){
        $sql = new Sql($this->getServiceLocator()->get('db'));
        $select = $sql->select(array('v' => 'venue'));
        $where = new \Zend\Db\Sql\Where();
        $select->where($isActive);
        /******/
  //       $spec = function (Where $string) {
		//     $where->where("MATCH(v.title) AGAINST(".$string." in boolean mode)");
		// };

		// $select->where($spec);

        /*******/
        $resultSet = array();
        $results = $sql->prepareStatementForSqlObject($select)->execute();
        $resultSet = new \Zend\Db\ResultSet\ResultSet();
        $resultSet->initialize($results);
        $resultSet = $resultSet->toArray();

        return $resultSet;
	}
  
	
}
