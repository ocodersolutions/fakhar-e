<?php
namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;

class ArticleAlertTable extends BasicTableAdapter {

	public function insert($alert){
		$userId = null;
       	$oAuth = $this->getServiceLocator()->get('AuthService');
        $userInfo = $oAuth->getIdentity();
        if($userInfo == null){
             $userId = 0;
        }else{
            $userId = $userInfo->userId;
        }
	   	$data = array(
            'email' => $alert['email'],
            'feeddataid' => $alert['feeddataid'],
            'userId' => $userId,
        );
        $result = $this->tableGateway->insert($data);
        if($result){
            $result = $this->tableGateway->lastInsertValue;
        }
        return $result;
	}
	public function delete($alert){
		$userId = null;
        $oAuth = $this->getServiceLocator()->get('AuthService');
        $userInfo = $oAuth->getIdentity();
        if($userInfo == null){
             $userId = 0;
        }else{
            $userId = $userInfo->userId;
        }
        $data = array(
            'email' => $alert['email'],
            'feeddataid' => $alert['feeddataid'],
            'userId' => $userId,
        );
        $result = $this->tableGateway->delete($data);
        return $result;
	}
  
	
}
