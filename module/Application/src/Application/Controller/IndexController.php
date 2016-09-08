<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Crypt\BlockCipher;
use Zend\Db\Sql\Where;

class IndexController extends AbstractActionController
{
    private $secretKey = 'AUvSAfcDTwEu6GIcBYtYUFQGX6nfWEVebeerSWpIkx13K';
    
    public function testAction()
    {
        $oJoinBetaTable = $this->getServiceLocator()->get('AdvertiserTable');
        $oJoinBetaTable->getReady( 3 );
        $oJoinBetaTable->getAttributes( 'Men\'s > Jeans > Straight Leg Jeans', 'this is test' );
        die;
    }

    public function indexAction()
    {
        
        // if ($this->getServiceLocator()->get('AuthService')->hasIdentity()) {
        //     $this->redirect()->toUrl('/service');
        // }
                
        $this->layout()->showHeaderLinks = "NOT_LOGGED_IN";
        $oAuth = $this->getServiceLocator()->get('AuthService');
        if( $oAuth->hasIdentity() ) {
            $this->layout()->showHeaderLinks = "LOGGED_IN";
            $userInfo = $oAuth->getIdentity();

            $this->layout()->firstName = $userInfo->firstName;
        }
    	$__viewVariables = array();
    	
    	$this->layout('layout/home_layout.phtml');
    	
    	return $__viewVariables;
    }
    
    public function comingsoonAction()
    {
        $__viewVariables = array();
        
        $aPostParams = $this->params()->fromPost();
        if (count($aPostParams)) {
            
            $oJoinBetaTable = $this->getServiceLocator()->get('JoinBetaTable');
            $oJoinBetaTable->addRecord($aPostParams);
            
            
           $__viewVariables['joinBetaComplete'] = true;
        }
        
        $this->layout('layout/empty_with_css.phtml');
        
        return $__viewVariables;
    }
    
    public function newsletterAction() 
    {
        switch ($_SERVER['HTTP_ORIGIN']) {
            case 'http://devblog.lnove.local': case 'http://devblog.elnove.com':
            case 'http://s.blog.elnove.com':
            header('Access-Control-Allow-Origin: '.$_SERVER['HTTP_ORIGIN']);
            header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
            header('Access-Control-Max-Age: 1000');
            header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
            break;
        }

        $aPostParams = $this->params()->fromPost();
        if (count($aPostParams)) {
            $oJoinBetaTable = $this->getServiceLocator()->get('JoinBetaTable');
            $email = $aPostParams['newsLetterInput'];
            if($this->checkStatus($email) =='update' ){
                $where = new Where();
                $where->equalTo('email',$email);
                $dataArr = array(
                    'data' => array(
                        'agreeToReceive' => 1
                    ),
                    'where' => $where,
                );
                $oJoinBetaTable->updateRecord($dataArr);
            } else if($this->checkStatus($email) == 'create' ) {
                $aData = array();
                $aData['joinBetaEmail'] = $email;
                $aData['agreeToReceive'] = 1;
                $aData['joinType'] = ((isset($aPostParams['from']) && !empty(isset($aPostParams['from']))) ? $aPostParams['from'] :  'HomeageNewsLetter');
                
                $oJoinBetaTable->addRecord($aData);
            }
            if($this->checkStatus($email) =='ignore' ) {
                echo json_encode(array('status'=>'success1'));   
                exit(0);
            }

            //send mail for registration
            //send email
            $cipher = BlockCipher::factory('mcrypt',  array(
                                    'algo' => 'blowfish',
                                    'mode' => 'cfb',
                                    'hash' => 'sha512'
                                ));
            $encryptemail = $cipher->setKey( $this->secretKey )->encrypt($email);
            $this->renderer = $this->getServiceLocator()->get('ViewRenderer');
            $oEmailService = $this->getServiceLocator()->get('Application\Service\EmailService');
            $layout = new ViewModel(['email'=> str_replace('/','-',$encryptemail)]);
            $layout->setTemplate('application/email/newsletter');
            
            $content = $this->renderer->render($layout); 
            $oEmailService->setSubject('Welcome to ELNove');
            $oEmailService->setHtmlBody($content);
            $oEmailService->setTo($email);
            $oEmailService->send();
        // end send mail
            
            
           echo json_encode(array('status'=>'success'));	
           exit(0);
        }
        
        echo json_encode(array('status'=>'error'));	
        exit(0);
    }  

    public function unsubcribedAction(){
        $params = $this->params('id');
        if(isset($params)) {
            $oJoinBetaTable = $this->getServiceLocator()->get('JoinBetaTable');
            $cipher = BlockCipher::factory('mcrypt',  array(
                                    'algo' => 'blowfish',
                                    'mode' => 'cfb',
                                    'hash' => 'sha512'
                                ));
            $cipher->setKey( $this->secretKey );
            $email = $cipher->decrypt( str_replace('-','/',$params ));
            if($this->checkStatus($email) == 'ignore') {
                $where = new Where();
                $where->equalTo('email',$email);
                $dataArr = array(
                    'data' => array(
                        'agreeToReceive' => 0
                    ),
                    'where' => $where,
                );
                $result = $oJoinBetaTable->updateRecord($dataArr);
            } else {
                $this->getResponse()->setStatusCode(404);
                return;
            }
            
        }   
    }     
    
    public function checkStatus($email){
        $oJoinBetaTable = $this->getServiceLocator()->get('JoinBetaTable');
        $row = $oJoinBetaTable->getItem(['email'=>$email]);
        if(isset($row->email) && $row->agreeToReceive == 0) {
            return 'update';
        } else if(isset($row->email) && $row->agreeToReceive == 1){
            return 'ignore';
        } else {
            return 'create';
        }
    }
}
