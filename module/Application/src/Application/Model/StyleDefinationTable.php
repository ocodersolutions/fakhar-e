<?php
namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;

class StyleDefinationTable extends BasicTableAdapter {
	protected $table = 'StyleDefination';
	protected $tableGateway;

	public function __construct(TableGateway $tableGateway){
	     $this->tableGateway = $tableGateway;
	}
	public function insert($attr, $value, $id){
        $value = implode(",",$value);
        $value = str_replace('+',' ',$value);
        $attr = str_replace('%2F','/',$attr);
    		$data = array(
            'styleId' => $id,
            'attribute' => $attr,
            'value' => $value,
        );
    	$result = $this->tableGateway->insert($data);
        return $result;
	}
    public function update($attr, $value, $number, $id ){

        $arr_style = array();
        foreach($value as $val){
            $arr_style[]  = str_replace('+',' ',$val);
        }
        $value = implode(",",$arr_style);
        $attr = str_replace('+',' ',$attr);
        $attr = str_replace('%2F','/',$attr);
           $data = array(
                "attribute" => $attr,
                "value" => $value
        
        );
           
        $update = $this->tableGateway->update($data, array("id" => $id));
        return $update;
    }
    public function delete($id ){
         
       $delete = $this->tableGateway->delete(array('id' => (int) $id));
       return $delete;
    }
	public function liststyle($id){
        $sql = new Sql($this->getServiceLocator()->get('db'));
        $select = $sql->select(array('sl' => 'StyleDefination'));
        $select->where(array('sl.styleId' => $id));

        $resultSet = array();
        $results = $sql->prepareStatementForSqlObject($select)->execute();
        $resultSet = new \Zend\Db\ResultSet\ResultSet();
        $resultSet->initialize($results);
        $resultSet = $resultSet->toArray();
       
        return $resultSet;
	}
  
	
}
	