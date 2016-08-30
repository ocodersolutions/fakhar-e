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
        return $result;
	}
	public function checkemailexist($email){
		
	}
  
	
}
