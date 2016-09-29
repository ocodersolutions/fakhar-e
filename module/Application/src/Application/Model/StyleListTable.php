<?php
namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;

class StyleListTable extends BasicTableAdapter {
	protected $table = 'Style';
	protected $tableGateway;

	public function __construct(TableGateway $tableGateway){
	     $this->tableGateway = $tableGateway;
	}
	public function insert($style){
       	$oAuth = $this->getServiceLocator()->get('AuthService');
        $userInfo = $oAuth->getIdentity();
		$userId = $userInfo == null ? 0 : $userInfo->userId;
		
	   	$data = array(
            'title' => $style['title'],
            'isActive' => $style['isActive'],
            'userId' => $userId,
        );
        $result = $this->tableGateway->insert($data);
        if($result){
            $result = $this->tableGateway->lastInsertValue;
        }
        return $result;
	}
	public function delete($id){
		$delete = $this->tableGateway->delete(array('id' => $id));
		return $delete;
	}
	public function update($userId, $aPostParams){
		$value = explode("&",$aPostParams['form']);
		$arr_style = [];
        foreach($value as $val){
        	$tmp = explode( '=', $val );
            $arr_style[ $tmp[0] ] = $tmp[1];
        }
      	$arr_style['name_style'] = str_replace('+',' ',$arr_style['name_style']);
      	$id = $arr_style['update-style'];


		$nextWeek = time() + (7 * 24 * 60 * 60);
		$timeupdate = date('Y-m-d H:i:s' );	
		$data = array(
			    "title" => $arr_style['name_style'],
			    "isActive" => $arr_style['status_select'],
			    "updated" => $timeupdate
		);

		
		$update = $this->tableGateway->update($data, array('id' => $id));
		return $update;
	}
	public function viewlist(){

		
        $sql = new Sql($this->getServiceLocator()->get('db'));
        $select = $sql->select(array('sl' => 'Style'));
       	
        $resultSet = array();
        $results = $sql->prepareStatementForSqlObject($select)->execute();
        $resultSet = new \Zend\Db\ResultSet\ResultSet();
        $resultSet->initialize($results);
        $resultSet = $resultSet->toArray();

        return $resultSet;
	}
	public function viewsingleitem($id){
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        return $row;
	}
		
}
	