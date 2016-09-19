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
use Zend\Http\Client;
use Facebook\FacebookSession;
use Facebook\FacebookJavaScriptLoginHelper;
use Ocoder\Facebook as OcoderFacebook;
use Zend\Session\Container;

use Facebook\Facebook as My_Facebook;
use Facebook\FacebookApp;
use Facebook\FacebookBatchRequest;
use Facebook\FacebookBatchResponse;
use Facebook\FacebookClient;
use Facebook\FacebookRequest;
use Facebook\FacebookResponse;
use Facebook\SignedRequest;
use Facebook\Helpers\FacebookRedirectLoginHelper;
use Facebook\Authentication\AccessToken;
use Facebook\Authentication\AccessTokenMetadata;
use Facebook\Authentication\OAuth2Client;
use Facebook\Helpers\FacebookJavaScriptHelper;
use Facebook\Helpers\FacebookCanvasHelper;
use Facebook\Helpers\FacebookPageTabHelper;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\GraphNodes\GraphNode;
use Facebook\GraphNodes\GraphEdge;
use Facebook\GraphNodes\GraphAchievement;
use Facebook\GraphNodes\GraphAlbum;
use Facebook\GraphNodes\GraphLocation;
use Facebook\GraphNodes\GraphPage;
use Facebook\GraphNodes\GraphPicture;
use Facebook\GraphNodes\GraphUser;

class AuthController extends AbstractActionController {

    protected $storage;
    protected $authservice;

    public function randchar( $char_number = 8 ){
        $seed = str_split('abcdefghijklmnopqrstuvwxyz'
                     .'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
                     .'0123456789!@#$%^&*()'); // and any other characters
            shuffle($seed); // probably optional since array_is randomized; this may be redundant
            $rand = '';
            foreach (array_rand($seed, $char_number) as $k) $rand .= $seed[$k];
         
            return $rand;
    }

    public function getAuth($email,$password){
    
        $oAuth = $this->getServiceLocator()->get('AuthService');
        $oAuth->getAdapter()
        ->setTableName('Users')
        ->setIdentity($email)
        ->setCredential($password);
        $result =  $oAuth->authenticate();
        if ($result->isValid()) {
    
            $oAuth->getStorage()->write($oAuth->getAdapter()->getResultRowObject());
            $oAuth->getStorage()->set('sessionId', uniqid('', true));
             
            $session = new Container('alert');
            $session->alert = true;
            return $this->redirect()->toUrl('/service');
        }
        return $this->redirect()->toUrl('/auth/login');
    
    }
    
    public function loginAction() 
    {
        $alertContainer = new Container('alert');
        $alertContainer->offsetUnset('alertList');
        if ($this->getAuthService()->hasIdentity()) {
            $this->redirect()->toUrl('/service');
        }
        $this->layout('layout/layout_elnove.phtml');
        return array();
    }

    public function authenticateAction() {
        $aPostParams = $this->params()->fromPost();
        if (count($aPostParams)) {
            $this->getAuthService()->getAdapter()
                    ->setTableName('Users')
                    ->setIdentity($aPostParams['email'])
                    ->setCredential(md5($aPostParams['password']));
                    
            if(isset($aPostParams['option']) && $aPostParams['option'] == 1) {        
				$cookie1 = new \Zend\Http\Header\SetCookie('emailcookie', $aPostParams['email'], time() + (((30*24)*60)*60));
				$cookie2 = new \Zend\Http\Header\SetCookie('passwordcookie', $aPostParams['password'], time() + (((30*24)*60)*60));
				$this->getResponse()->getHeaders()->addHeader($cookie1);
				$this->getResponse()->getHeaders()->addHeader($cookie2);
			} else {
				$prevdatetime = strtotime('-1 day', strtotime(date('Y-m-d')));					
				$cookie1 = new \Zend\Http\Header\SetCookie('emailcookie', $aPostParams['email'], $prevdatetime);
				$cookie2 = new \Zend\Http\Header\SetCookie('passwordcookie', $aPostParams['password'], $prevdatetime);
				$this->getResponse()->getHeaders()->addHeader($cookie1);
				$this->getResponse()->getHeaders()->addHeader($cookie2);									
			}

            $result = $this->getAuthService()->authenticate();

            if ($result->isValid()) {
                $oAuth = $this->getAuthService();
                $oAuth->getStorage()->write($this->getAuthService()->getAdapter()->getResultRowObject());
                $oAuth->getStorage()->set('sessionId', uniqid('', true));
                return $this->redirect()->toUrl('/service');
            } else {
                return $this->redirect()->toUrl('/auth/login?status=-1');
            }
        }
    }

    public function getAuthService() {
        if (!$this->authservice) {
            $this->authservice = $this->getServiceLocator()->get('AuthService');
        }

        return $this->authservice;
    }

    public function logoutAction() {
        $alertContainer = new Container('alert');
        $alertContainer->offsetUnset('alertList');
        $this->getAuthService()->clearIdentity();
        //$this->flashmessenger()->addMessage("You've been logged out");
        return $this->redirect()->toUrl('/');
    }
    

        
    public function getAuthFacebook($facebook_id, $new=false){

        $oUser = $this->getServiceLocator()->get('UsersTable');
        $aUserData = $oUser->getUserBy('facebook_id', $facebook_id);
        
        if ( isset($aUserData['userId']) ) {
            $oAuth = $this->getAuthService();
            $oAuth->getStorage()->write( (object)$aUserData );
            $oAuth->getStorage()->set('sessionId', uniqid('', true));
         
            if($new) {
                $session = new Container('alert');
                $session->alert = true;
            }
            echo  json_encode(array('status'=>'success'));
        }
        else {
            echo json_encode(array('status'=>'fail'));
        }
        die;
    }

    /* Function to create user or update user while login via facebook
    Author  :   Punit Kumar
    Access  :   Public
    Created :   04-10-2014
    @params :   User details fetched from facebook api */
  //   public function fbLoginAction() 
  //   {
  //        $oUsers = $this->getServiceLocator()->get('UsersTable');
  //        // echo '<pre style="color:blue">';
  //        // var_dump($oUsers->demo());
  //        // echo '</pre>';    
  //        // die(__FILE__);
  //       $aPostParams = $this->params()->fromPost();
          
      

  //       if(isset($aPostParams['action']) && $aPostParams['action'] == 'login')
  //       {
  //           $AppID = '678546158918666'; $AppSecret = '157302e15c7ba469a5afdc379729dad7';    // main domain - elnove.com
  //           if($this->getRequest()->getUri()->getHost()=='dev.elnove.com') 			{ $AppID = '921413221278386'; $AppSecret = '2238a8a51697452d02feca4f976727a0';}
  //           else if($this->getRequest()->getUri()->getHost()=='staging.elnove.com') 	{ $AppID = '1430879530553959'; $AppSecret = '5514b94a65e5d710d5ae5e881086988e';}
            
  //           else if($this->getRequest()->getUri()->getHost()=='elnove.local')    { $AppID = '1592468217691142'; $AppSecret = 'fc28f132949b146de63b00f2f84034cd';}       
            
  //           session_start();
  //           FacebookSession::setDefaultApplication($AppID, $AppSecret);
  //           $helper = new FacebookJavaScriptLoginHelper();
  //           $session = $helper->getSession();
  //           //print_r($session);
                        
  //           $oUsers = $this->getServiceLocator()->get('UsersTable');
  //           if($oUsers->isUnique('email', $aPostParams['email']) || !$session)
  //           {
  //               $data = array(
  //                   'status' => 'fail',
  //                   'msg' => 'Not registered'
  //               );
  //               echo json_encode($data);
  //               die;
  //           }
  //           else {
  //           	$this->getAuthService()->getAdapter()
	 //            	->setTableName('Users')
	 //            	->setIdentity($aPostParams['email'])
	 //            	->setCredential(md5('123456'));
            	
  //           	$result = $this->getAuthService()->authenticate();
            	
  //           	if ($result->isValid()) {
            	
  //           		$oAuth = $this->getAuthService();
  //           		$oAuth->getStorage()->write($this->getAuthService()->getAdapter()->getResultRowObject());
  //           		$oAuth->getStorage()->set('sessionId', uniqid('', true));
		//             $data = array(
		//                 'status' => 'success'
		//             );
  //           	} else {
	 //                $data = array(
	 //                    'status' => 'fail',
	 //                    'msg' => 'Invalid credentials'
	 //                );
  //           	}

  //           	echo json_encode($data);
  //           	die;
            	 
  //           }
  //       }
  //       $data = array(
  //           'email' 			=> $aPostParams['email'],
		// 	'password' 			=> md5('123456'),
		// 	'firstName' 		=> $aPostParams['firstName'],
		// 	'lastName' 			=> $aPostParams['lastName'],
		// 	'dob' 				=> date('Y-m-d', strtotime($aPostParams['dob'])),
		// 	'defaultVenue' 		=> 'Test',
  //           'userType'          => '2',
  //       );
  //       //    $data = array(
  //       //     'email'          => 'tuanmythkt@gmail.com',
  //       //     'password'           => md5('123456'),
  //       //     'firstName'      => 'Tuanmy Tran',
  //       //     'lastName'           => 'My',
  //       //     'dob'                => date('Y-m-d', strtotime('1992-05-02')),
  //       //     'defaultVenue'       => 'Test',
  //       //     'userType'          => '2',
  //       // );
                 

  //       if(count($aPostParams)) {

		// 	$oUsers = $this->getServiceLocator()->get('UsersTable');

		// 	if( !empty($aPostParams['email']) ) 
  //           {
		// 	     if($oUsers->isUnique('email', $aPostParams['email'])) // ko tồn tại ở db
  //                {
  //                   $iUserId = $oUsers->createUser( $data );
                     
  //                   if(!isset($aPostParams['action']))
  //                   {
  //                       // Update user id with user's activity
  //                       //$event = new \Event\Model\EventsLog();
  //                       //$sm = $this->getServiceLocator();
  //                       //$eFeeds = $sm->get('EventsLogTable');
  //                       //$eFeeds->updateUserLog($this->get_client_ip(), $iUserId);
  //                   }
  //                }
  //                else // có tồn tại
  //                {

  //                   $id = $oUsers->getUserByEmail('email', $aPostParams['email']);
  //                   $data['userId'] = $id;
  //                   $oUsers->createUser( $data );
  //                }
		// 	}
  //           $data = array(
  //               'status' => 'success',
  //               'msg' => 'Registered'
  //           );
  //           echo json_encode($data);
  //           die;
		// }
  //   }

    public function fbLoginAction(){
        error_reporting(E_ALL & ~E_NOTICE);
        header('Content-Type: application/json');
        
        $AppID = '678546158918666'; $AppSecret = '157302e15c7ba469a5afdc379729dad7';    // main domain - elnove.com
        if($this->getRequest()->getUri()->getHost()=='dev.elnove.com')       { $AppID = '921413221278386'; $AppSecret = '2238a8a51697452d02feca4f976727a0';}
        else if($this->getRequest()->getUri()->getHost()=='staging.elnove.com')  { $AppID = '1430879530553959'; $AppSecret = '5514b94a65e5d710d5ae5e881086988e';}
        else if($this->getRequest()->getUri()->getHost()=='trung.elnove.com')    { $AppID = '197600357248915'; $AppSecret = '960209f57141634c3c6dd977ea102145';} 
        else if($this->getRequest()->getUri()->getHost()=='devmobile.elnove.com')    { $AppID = '1700806690132207'; $AppSecret = 'e49e790863b12eca2c9e315e403cb996';}  
        else if($this->getRequest()->getUri()->getHost()=='m.lnove.local')    { $AppID = '649739978501638'; $AppSecret = '147f228ec230d04cff6d00cd4264723b';} 
        else if($this->getRequest()->getUri()->getHost()=='lnove.local')    { $AppID = '1238074509543418'; $AppSecret = '863934f1c9e4a471fc809badcc519ea4';}  
        else if($this->getRequest()->getUri()->getHost()=='s.m.elnove.com')    { $AppID = '420904578119853'; $AppSecret = '0499b5efbe2e5ed2e8b4c148aa7ea52e';}
        $fb = new My_Facebook([
          'app_id' => $AppID,
          'app_secret' => $AppSecret
          ]);
        
        $aPostParams = $this->params()->fromPost();
        
        $jsHelper = $fb->getJavaScriptHelper();
        // @TODO This is going away soon
        $facebookClient = $fb->getClient();

        try {
            $accessToken = $jsHelper->getAccessToken($facebookClient);

        } catch(Facebook\Exceptions\FacebookResponseException $e) {
            // When Graph returns an error
            echo 'Graph returned an error: ' . $e->getMessage();
        } catch(Facebook\Exceptions\FacebookSDKException $e) {
            // When validation fails or other local issues
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
        }

        if (isset($accessToken)) {
            // Logged in.
            $_SESSION['fb_access_token'] = (string) $accessToken; 
              try {
          // Returns a `Facebook\FacebookResponse` object
              $response = $fb->get('/me?fields=id,name,first_name,last_name,email,birthday', $accessToken->getValue());
            } catch(Facebook\Exceptions\FacebookResponseException $e) {
              echo 'Graph returned an error: ' . $e->getMessage();
              exit;
            } catch(Facebook\Exceptions\FacebookSDKException $e) {
              echo 'Facebook SDK returned an error: ' . $e->getMessage();
              exit;
            }

            $user = $response->getGraphUser();
            $dob = null;
            if($user['birthday'] instanceof \DateTime){
              $dob =  $user['birthday']->format('Y-m-d');
            }
            $userTable = $this->getServiceLocator()->get('UsersTable');
            $oAuth = $this->getServiceLocator()->get('AuthService');
            $email = isset($user['email']) ? $user['email']: '';
            if($user['id']) {
                  $user_data = $userTable->getUserBy('facebook_id', $user['id']);
                  if($user_data){
                      return $this->getAuthFacebook($user['id']); 
                  } else {
                      
                      if($email) {
                            $aUser = $userTable->getUserBy('email',$email);
                            if( isset($aUser['userId']) ) {
                                $userTable->updateUser($aUser['userId'], array('facebook_id'=>$user['id']));
                                return $this->getAuthFacebook($user['id']);
                            }
                      }
                      
                      if( isset($aPostParams['action']) && $aPostParams['action']=='login_only' ) {
                          echo json_encode(array('status'=>'fail'));
                          die;
                      }
                      
                         $data = array(
                              'email'             => $email,
                              'facebook_id'       => $user['id'],
                              'password'          => '',
                              'firstName'         => $user['first_name'],
                              'lastName'          => $user['last_name'],
                              'defaultVenue'      => 'Work',
                              'userType'          => '2',
                              'dob'               => $dob,
                          );
                      $iUserId = $userTable->createUserSocial($data);
                      
                      if($email) {
                          //send email
                          $this->renderer = $this->getServiceLocator()->get('ViewRenderer');
                          $oEmailService = $this->getServiceLocator()->get('Application\Service\EmailService');
                          
                          $oUsers = $this->getServiceLocator()->get('UsersTable');
                          $user_info =  $oUsers->getUserInfo('email',$email);
                          
                          $layout = new ViewModel(
                              ['user_info' => $user_info]
                          );
                          $layout->setTemplate('application/email/signup');
                          
                          $content = $this->renderer->render($layout);
                          
                          $oEmailService->setSubject('Welcome to ELNove');
                          $oEmailService->setHtmlBody($content);
                          $oEmailService->setTo($email);
                          $oEmailService->send();
                          // end send mail        
                      }              
                      
                      
                      
                      return $this->getAuthFacebook( $user['id'], true);
                  }

               
            }        
        } else {
            // Unable to read JavaScript SDK cookie
            return $this->redirect()->toUrl('/auth/login');
        }

    }
    
    function get_client_ip() {
        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP'))
            $ipaddress = getenv('HTTP_CLIENT_IP');
        else if(getenv('HTTP_X_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        else if(getenv('HTTP_X_FORWARDED'))
            $ipaddress = getenv('HTTP_X_FORWARDED');
        else if(getenv('HTTP_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        else if(getenv('HTTP_FORWARDED'))
           $ipaddress = getenv('HTTP_FORWARDED');
        else if(getenv('REMOTE_ADDR'))
            $ipaddress = getenv('REMOTE_ADDR');
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }

}

