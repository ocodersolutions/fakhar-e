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
use Application\Model\UsersTable;
use Application\Service\StorageService;
use Zend\Mvc\MvcEvent;
use Zend\Session\Container;
use \Event\Model\Event as Event;
use Zend\Mail;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;
use Zend\Json\Json;
use Zend\Session\SessionManager;
use Zend\Validator\File\Size;
use Zend\Crypt\BlockCipher;

class ProfileController extends AbstractActionController {

    private $oSession = null;
    private $oUserProfile = null;
    private $secretKey = 'AUvSAfcDTwEu6GIcBYtYUFQGX6nfWEVebeerSWpIkx13K';
    

    public function onDispatch(\Zend\Mvc\MvcEvent $e) {
        //$this->oSession = new Container('profile');

        $oService = $this->getServiceLocator();
        $this->oSession = $oService->get('StorageService')->getSession();
        $this->oUserProfile = $oService->get('UserProfilesTable');

// 		$oAuth = $this->getServiceLocator()->get('AuthService');
// 		$aProfile = array();
// 		if( $oAuth->hasIdentity() ) {
// 			$oUserProfile = $this->getServiceLocator()->get('UserProfilesTable');
// 			$aProfile = $oUserProfile->getUserProfile();
// 		}


        $this->layout()->showFooter = false;
        $this->layout()->showSmallHeader = true;

        return parent::onDispatch($e);
    }

    function get_client_ip() {
        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP'))
            $ipaddress = getenv('HTTP_CLIENT_IP');
        else if (getenv('HTTP_X_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        else if (getenv('HTTP_X_FORWARDED'))
            $ipaddress = getenv('HTTP_X_FORWARDED');
        else if (getenv('HTTP_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        else if (getenv('HTTP_FORWARDED'))
            $ipaddress = getenv('HTTP_FORWARDED');
        else if (getenv('REMOTE_ADDR'))
            $ipaddress = getenv('REMOTE_ADDR');
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }

    public function indexAction() {
        $oService = $this->getServiceLocator();
        $ouserTraking = $oService->get('UserTrakingTable');
        $sessionManager = new SessionManager();
        $sessionid = $sessionManager->getId();
        $this->oSession->data = array();
        $this->layout()->showSmallHeader = true;

        $this->layout()->showHeaderLinks = "NOT_LOGGED_IN";
        $oAuth = $this->getServiceLocator()->get('AuthService');
        if ($oAuth->hasIdentity()) {
            $this->layout()->showHeaderLinks = "LOGGED_IN";
            $userInfo = $oAuth->getIdentity();

            $this->layout()->firstName = $userInfo->firstName;
        }
        $__viewVariables = array();

        $aStyles = $this->oUserProfile->getStyleQuestions();

        $aPostParams = $this->params()->fromPost();
         
        //print_r($aStyles); die;
        if (count($aPostParams)) {
            $this->eventLog('profile-style', $aPostParams); // log event

            $aSessionData = array();
            foreach ($aStyles as $styleKey => $styleValue) {
                foreach ($styleValue as $styleItem) {
                    if (isset($aPostParams[$styleItem['name']]) && $aPostParams[$styleItem['name']] == 'on') {
                        if (!isset($aSessionData[$styleKey])){
                            $aSessionData[$styleKey] = array();
                        }
                        $aSessionData[$styleKey][] = $styleItem['name'];
                    }
                }
            }
       
            $data['profile'] = $aSessionData;
            $profileData = Json::encode($data);
            $userData['data'] = $profileData;
    
            $this->oSession->data['style'] = $aSessionData;

            if ($oAuth->hasIdentity()) {
                $oUserProfile = $this->getServiceLocator()->get('UserProfilesTable');
                $oUserProfile->updateUserProfile($oAuth->getIdentity()->userId, $this->oSession->data);
            } else {
            	 $result = $ouserTraking->updateProfileTraking($userData, $sessionid, 'profile');	
            }


            $this->redirect()->toUrl('/profile/you');
        }
        
        $__viewVariables['mode'] = $oAuth->hasIdentity() ? 'edit' : 'new';
        $__viewVariables = array_merge($__viewVariables, $aStyles);
        $this->layout('layout/layout_elnove.phtml');
        return $__viewVariables;
    }

    /*
     * Function for profile traking page
     * 
     * @param fieldId string name of block for where user click on block
     * 
     * @return bool true or false if traking inserted into tables
     * 
     */

    public function profiletrakingAction() {
        $data = array();
        $oService = $this->getServiceLocator();
        $oAuth = $oService->get('AuthService');
        $userInfo = $oAuth->getIdentity();
        $sessionManager = new SessionManager();
        $sessionid = $sessionManager->getId();
        $ip = $this->get_client_ip();
        $data['session_id'] = $sessionid;
        $data['system_ip'] = $ip;
        $request = $this->getRequest();
        $postData = $request->getPost();
        $page = $postData['page'];
        $data['page'] = $page;
        if ($oAuth->hasIdentity()) {
            $userId = $userInfo->userId;
            $data['user_id'] = $userId;
        } else {
            $userId = '';
        }
        $data['created_date'] = date('Y-m-d h:i:s');
        $ouserTraking = $oService->get('UserTrakingTable');
        $userTrakingInfo = $ouserTraking->getUserTrakingInfo($sessionid, $page);
        if (isset($userTrakingInfo) && empty($userTrakingInfo)) {
            $result = $ouserTraking->saveProfileTraking($data);
        } else {
            if (isset($userId) && !empty($userId) && empty($userTrakingInfo->user_id)) {
                $userData['user_id'] = $userId;
                $result = $ouserTraking->updateProfileTraking($userData, $sessionid);
            }
        }

        $status['status'] = true;
        return $this->getResponse()->setContent(Json::encode($status));
    }

    public function youAction() {
 
        $oService = $this->getServiceLocator();
        $ouserTraking = $oService->get('UserTrakingTable');
        $sessionManager = new SessionManager();
        $sessionid = $sessionManager->getId();
        $this->layout()->showSmallHeader = true;

        $this->layout()->showHeaderLinks = "NOT_LOGGED_IN";
        $oAuth = $this->getServiceLocator()->get('AuthService');
        if ($oAuth->hasIdentity()) {
            $this->layout()->showHeaderLinks = "LOGGED_IN";
            $userInfo = $oAuth->getIdentity();

            $this->layout()->firstName = $userInfo->firstName;
        }

        if (!isset($this->oSession->data['style'])) {
            $this->redirect()->toUrl('/profile');
        }

        $__viewVariables = array();

        $aStyles = $this->oUserProfile->getAboutYouQuestions();

        $aPostParams = $this->params()->fromPost();
        if (count($aPostParams)) {
            $this->eventLog('profile-you', $aPostParams); // log event
            $aSessionData = array();
            foreach ($aStyles as $styleKey => $styleValue) {
                foreach ($styleValue as $styleItem) {
                    if(isset($styleItem['name'])) {
                        if (isset($aPostParams[$styleItem['name']]) && $aPostParams[$styleItem['name']] == 'on') {
                            if (!isset($aSessionData[$styleKey]))
                                $aSessionData[$styleKey] = array();
                                $aSessionData[$styleKey] = $styleItem['name'];
                        }
                    }
                    
                }
            }
            $aSessionData['weight'] = $aPostParams['Weight'];
            $aSessionData['height'] = $aPostParams['Height'];
            $data['you'] = $aSessionData;
            $profileData = Json::encode($data);
            $userData['data'] = $profileData;
            $this->oSession->data['you'] = $aSessionData;
            
            if ($oAuth->hasIdentity()) {
                $oUserProfile = $this->getServiceLocator()->get('UserProfilesTable');
                $oUserProfile->updateUserProfile($oAuth->getIdentity()->userId, $this->oSession->data);
            } else { 
					 $result = $ouserTraking->updateProfileTraking($userData, $sessionid, 'you');            
            }
            $this->redirect()->toUrl('/profile/size');
        }
        
        $__viewVariables['mode'] = $oAuth->hasIdentity() ? 'edit' : 'new';

        $__viewVariables = array_merge($__viewVariables, $aStyles);
        $this->layout('layout/layout_elnove.phtml');
        return $__viewVariables;
    }

    public function sizeAction() {
        
        $oHelper = $this->getServiceLocator()->get('viewhelpermanager');
        $oHelper->get('HeadScript')->appendFile('/js/jquery.ddslick.js'); 
                
        $oService = $this->getServiceLocator();
        $ouserTraking = $oService->get('UserTrakingTable');
        $sessionManager = new SessionManager();
        $sessionid = $sessionManager->getId();
        $this->layout()->showSmallHeader = true;

        $this->layout()->showHeaderLinks = "NOT_LOGGED_IN";
        $oAuth = $this->getServiceLocator()->get('AuthService');
        if ($oAuth->hasIdentity()) {
            $this->layout()->showHeaderLinks = "LOGGED_IN";
            $userInfo = $oAuth->getIdentity();

            $this->layout()->firstName = $userInfo->firstName;
        }
        if (!isset($this->oSession->data['you'])) {
            $this->redirect()->toUrl('/profile/you');
        }

        $__viewVariables = array();
        
        $aStyles = $this->oUserProfile->getSizeQuestions();

        $aPostParams = $this->params()->fromPost();
        if (count($aPostParams)) {
            $this->eventLog('profile-size', $aPostParams); // log event
            $this->oSession->data['size'] = $aPostParams;

            $data['size'] = $aPostParams;
            $profileData = Json::encode($data);
            $userData['data'] = $profileData;
            
            if ($oAuth->hasIdentity()) {
                $oUserProfile = $this->getServiceLocator()->get('UserProfilesTable');
                $oUserProfile->updateUserProfile($oAuth->getIdentity()->userId, $this->oSession->data);
            } else { 
					 $result = $ouserTraking->updateProfileTraking($userData, $sessionid, 'size');          
            }            

            $this->redirect()->toUrl('/profile/price');
        }
        
        $__viewVariables['mode'] = $oAuth->hasIdentity() ? 'edit' : 'new';

        $__viewVariables = array_merge($__viewVariables, $aStyles);
        $this->layout('layout/layout_elnove.phtml');
        return $__viewVariables;
    }

    public function brandsAction() {
        $oService = $this->getServiceLocator();
        $ouserTraking = $oService->get('UserTrakingTable');
        $sessionManager = new SessionManager();
        $sessionid = $sessionManager->getId();
        $this->layout()->showSmallHeader = true;

        $this->layout()->showHeaderLinks = "NOT_LOGGED_IN";
        $oAuth = $this->getServiceLocator()->get('AuthService');
        if ($oAuth->hasIdentity()) {
            $this->layout()->showHeaderLinks = "LOGGED_IN";
            $userInfo = $oAuth->getIdentity();

            $this->layout()->firstName = $userInfo->firstName;
        }
        $__viewVariables = array();

        $__viewVariables['brands'] = $this->oUserProfile->getBrandQuestions();

        $aPostParams = $this->params()->fromPost();
        if (count($aPostParams)) {
            $this->eventLog('profile-brands', $aPostParams); // log event
            $aBrands = array();
            foreach ($aPostParams as $key => $val) {
                if ($val == 'on' && preg_match('/^brand([0-9]+)/', $key)) {
                    $aBrands[] = str_replace('brand', '', $key);
                }
            }
            $this->oSession->data['brands'] = $aBrands;

            $data['brands'] = $aBrands;
            $profileData = Json::encode($data);
            $userData['data'] = $profileData;

            if ($oAuth->hasIdentity()) {
                $oUserProfile = $this->getServiceLocator()->get('UserProfilesTable');
                $oUserProfile->updateUserProfile($oAuth->getIdentity()->userId, $this->oSession->data);
            } else { 
					 $result = $ouserTraking->updateProfileTraking($userData, $sessionid, 'brands');            
            } 
            
            $this->redirect()->toUrl('/profile/price');
        }

        return $__viewVariables;
    }

    public function priceAction() {
        $this->layout("layout/layout_elnove");
        $oService = $this->getServiceLocator();
        $ouserTraking = $oService->get('UserTrakingTable');
        $sessionManager = new SessionManager();
        $sessionid = $sessionManager->getId();
        $this->layout()->showSmallHeader = true;

        $this->layout()->showHeaderLinks = "NOT_LOGGED_IN";
        $oAuth = $this->getServiceLocator()->get('AuthService');
        if ($oAuth->hasIdentity()) {
            $this->layout()->showHeaderLinks = "LOGGED_IN";
            $userInfo = $oAuth->getIdentity();

            $this->layout()->firstName = $userInfo->firstName;
        }

        if (!isset($this->oSession->data['size'])) {
            $this->redirect()->toUrl('/profile/size');
        }

        $oAuth = $this->getServiceLocator()->get('AuthService');

        $__viewVariables = array();
        if ($oAuth->hasIdentity()) {
            $__viewVariables['logInStatus'] = true;
        } else {
            $__viewVariables['logInStatus'] = false;
        }

        $__viewVariables['price'] = $this->oUserProfile->getPriceQuestions();

        $aPostParams = $this->params()->fromPost();
        if (count($aPostParams)) {
            $this->eventLog('profile-price', $aPostParams); // log event
            $aPrices = array();
            foreach ($__viewVariables['price'] as $key => $val) {
                $aPrices[$val['name']] = @$aPostParams[$val['name']];
            }
            $data['price'] = $aPrices;
            $profileData = Json::encode($data);
            $userData['data'] = $profileData;
            
            $this->oSession->data['prices'] =$aPrices;
          
            if ($oAuth->hasIdentity()) {
                $oUserProfile = $this->getServiceLocator()->get('UserProfilesTable');
                $oUserProfile->updateUserProfile($oAuth->getIdentity()->userId, $this->oSession->data);
                $this->redirect()->toUrl('/profile/myaccount?sm=yes');
            } else {
            	 $result = $ouserTraking->updateProfileTraking($userData, $sessionid, 'price');
            	 $this->redirect()->toUrl('/profile/signup');
            }
        }
        
        $__viewVariables['mode'] = $oAuth->hasIdentity() ? 'edit' : 'new';


        return $__viewVariables;
    }

    public function signupAction() {
        $this->layout("layout/layout_elnove");
		ini_set('max_execution_time', 0);
        $__viewVariables = array();

        $oAuth = $this->getServiceLocator()->get('AuthService');
        if ($oAuth->hasIdentity()) {

            $oUserProfile = $this->getServiceLocator()->get('UserProfilesTable');
            $oUserProfile->updateUserProfile($oAuth->getIdentity()->userId, $this->oSession->data);
            $this->redirect()->toUrl('/service');
        }

        if (!isset($this->oSession->data['prices'])) {
            $this->redirect()->toUrl('/profile/price');
        }

        $aPostParams = $this->params()->fromPost();
        if (count($aPostParams)) {
            $oUsers = $this->getServiceLocator()->get('UsersTable');

            if (!empty($aPostParams['email']) && $oUsers->isUnique('email', $aPostParams['email'])) {
                $iUserId = $oUsers->createUser($aPostParams);
                $oUserProfile = $this->getServiceLocator()->get('UserProfilesTable');
                $oUserProfile->updateUserProfile($iUserId, $this->oSession->data);
                $oAuth->getAdapter()
                    ->setTableName('Users')
                    ->setIdentity($aPostParams['email'])
                    ->setCredential(md5($aPostParams['password']));
                $result =  $oAuth->authenticate();
   
                 if ($result->isValid()) {
                     $oAuth->getStorage()->write($oAuth->getAdapter()->getResultRowObject());
                    $oAuth->getStorage()->set('sessionId', uniqid('', true));
                    
                    $session = new Container('alert');
                    $session->alert = true;
                
                    //send email
                    $this->renderer = $this->getServiceLocator()->get('ViewRenderer');
                    $oEmailService = $this->getServiceLocator()->get('Application\Service\EmailService');

                    $oUsers = $this->getServiceLocator()->get('UsersTable');
                    $user_info =  $oUsers->getUserInfo('email',$aPostParams['email']);

                    $layout = new ViewModel(
                        ['user_info' => $user_info]
                    );
                    $layout->setTemplate('application/email/signup');
                    
                    $content = $this->renderer->render($layout); 

                    $oEmailService->setSubject('Welcome to ELNove');
                    $oEmailService->setHtmlBody($content);
                    $oEmailService->setTo($aPostParams['email']);
                    $oEmailService->send();
                    // end send mail

                    return $this->redirect()->toUrl('/service');
                 }
                // Update user id with user's activity
                //  $event = new \Event\Model\EventsLog();
                //$sm = $this->getServiceLocator();
                //$eFeeds = $sm->get('EventsLogTable');
                //$eFeeds->updateUserLog($this->get_client_ip(), $iUserId);

                return $this->redirect()->toUrl('/auth/login');
                
            }
        }

        return $__viewVariables;
    }

    public function forgotpasswordAction() {
        $__viewVariables = array();

        $oAuth = $this->getServiceLocator()->get('AuthService');
        if ($oAuth->hasIdentity()) {
            $oUserProfile = $this->getServiceLocator()->get('UserProfilesTable');
            $oUserProfile->updateUserProfile($oAuth->getIdentity()->userId, $this->oSession->data);
            $this->redirect()->toUrl('/service');
        }

        $aPostParams = $this->params()->fromPost();
        if (count($aPostParams)) {
            $oUsers = $this->getServiceLocator()->get('UsersTable');
            $user_info =  $oUsers->getUserInfo('email',$aPostParams['email']);
            $cipher = BlockCipher::factory('mcrypt',  array(
                                'algo' => 'blowfish',
                                'mode' => 'cfb',
                                'hash' => 'sha512'
                            ));
            $encryptemail = $cipher->setKey( $this->secretKey )->encrypt( $aPostParams['email'] );
            $url = 'http://' . $_SERVER['SERVER_NAME'] . '/user/changepassword/' . str_replace('/','-',$encryptemail);

            $this->renderer = $this->getServiceLocator()->get('ViewRenderer');
            $layout = new ViewModel([
				'url' => $url,
				'service_name'  => $_SERVER['SERVER_NAME'],
				'user_info'		=> $user_info
            ]);
            $layout->setTemplate('application/email/forgotpassword');
            $body = $this->renderer->render($layout); 

            $oEmailService = $this->getServiceLocator()->get('Application\Service\EmailService');
            $oEmailService->setTo($aPostParams['email']);
            $oEmailService->setSubject('Your request on elnove to change password.');
            $oEmailService->setHtmlBody( $body );
            $oEmailService->send(); 
            $ssSystem  = new Container('alert');
            $ssSystem->alert = 'You will receive an email with instructions on how to reset your password in a few minutes.';
        }

        return new ViewModel($__viewVariables);
    }

    public function eventLog($page, $data) {
        $event = new \Event\Model\EventsLog();
        $event->user_ip = $this->get_client_ip();
        $event->page = $page;
        $event->product_selected = json_encode($data);
        $event->created = date("Y-m-d h:i:s");
        $sm = $this->getServiceLocator();
        $eFeeds = $sm->get('EventsLogTable');
        $event_id = $eFeeds->saveEvent($event);
        return true;
    }

    public function myaccountAction() {

        $this->layout("layout/layout_elnove.phtml");
        $aQueryParams = $this->params()->fromQuery();
        
        $__viewVariables = array();
        
        $__viewVariables['editSuccessfull'] = (isset($aQueryParams['sm']) ? true : false);

        if ($this->flashmessenger()->hasMessages()) {
            $flashmessages = $this->flashmessenger()->getMessages();
            $__viewVariables['flashmessage'] = $flashmessages[0];
        }

        $this->layout()->showSmallHeader = true;

        $this->layout()->showHeaderLinks = "NOT_LOGGED_IN";
        $oAuth = $this->getServiceLocator()->get('AuthService');
        if ($oAuth->hasIdentity()) {
            $this->layout()->showHeaderLinks = "LOGGED_IN";
            $userInfo = $oAuth->getIdentity();

            $this->layout()->firstName = $userInfo->firstName;
        }

        $oAuth = $this->getServiceLocator()->get('AuthService');
        if ($oAuth->hasIdentity()) {
            $oUsers = $this->getServiceLocator()->get('UsersTable');
            $__viewVariables['userdata'] = $oUsers->getUserInfoById($oAuth->getIdentity()->userId);

            $aPostParams = $this->params()->fromPost();
            if (count($aPostParams)) {

                $aPostParams['userId'] = $oAuth->getIdentity()->userId;
                $aPostParams['dob'] = $aPostParams['year'] . '-' . $aPostParams['month'] . '-' . $aPostParams['day'];

                $iUserId = $oUsers->updateMyaccount($aPostParams);
                $__viewVariables['editSuccessfull'] = true;

                $__viewVariables['userdata'] = $oUsers->getUserInfoById($oAuth->getIdentity()->userId);
                $infoUser = $oAuth->getStorage()->read();
                $infoUser->firstName = $__viewVariables['userdata']['firstName'];
            }
        } else {
            $this->redirect()->toUrl('/profile');
        }

        return $__viewVariables;
    }

    public function resetpasswordAction() {
        $__viewVariables = array();

        $this->layout()->showSmallHeader = true;

        $this->layout()->showHeaderLinks = "NOT_LOGGED_IN";
        $oAuth = $this->getServiceLocator()->get('AuthService');
        if ($oAuth->hasIdentity()) {
            $this->layout()->showHeaderLinks = "LOGGED_IN";
            $userInfo = $oAuth->getIdentity();

            $this->layout()->firstName = $userInfo->firstName;
        }

        $oAuth = $this->getServiceLocator()->get('AuthService');
        if ($oAuth->hasIdentity()) {
            $aPostParams = $this->params()->fromPost();
            if (count($aPostParams)) {

                $aPostParams['userId'] = $oAuth->getIdentity()->userId;

                $oUsers = $this->getServiceLocator()->get('UsersTable');
                $iUserId = $oUsers->updatePassword($aPostParams);

                $this->flashmessenger()->addMessage("Your password has been successfully changed.");
                $this->redirect()->toUrl('/profile/myaccount');
            }
        } else {
            $this->redirect()->toUrl('/profile');
        }

        return $__viewVariables;
    }

    public function uploadAction() {

        $__viewVariables = array();

        $this->layout()->showSmallHeader = true;

        $this->layout()->showHeaderLinks = "NOT_LOGGED_IN";
        $oAuth = $this->getServiceLocator()->get('AuthService');
        if ($oAuth->hasIdentity()) {
            $this->layout()->showHeaderLinks = "LOGGED_IN";
            $userInfo = $oAuth->getIdentity();

            $this->layout()->firstName = $userInfo->firstName;
        }

        $oAuth = $this->getServiceLocator()->get('AuthService');

        if ($oAuth->hasIdentity()) {
            $userId = $oAuth->getIdentity()->userId;
            $oUsers = $this->getServiceLocator()->get('UsersTable');
            $__viewVariables['userdata'] = $oUsers->getUserInfoById($userId);
            $aPostParams = $this->params()->fromPost();
            $File = $this->params()->fromFiles('profilepic');
            if (isset($File) && !empty($File)) {
                $size = new Size(array('min' => 100)); //minimum bytes filesize
                $adapter = new \Zend\File\Transfer\Adapter\Http();
                if (!$adapter->isValid()) {
                    
                } else {
                    $destinationPath = PUBLIC_PATH . '/img/profilepic/';
                    $ppName = time() . $userId . '_' . $File['name'];
                    $profilePic = $destinationPath . $ppName;
                    $adapter->addFilter('Rename', array('target' => $profilePic));
                    @chmod($profilePic, 0777);
                    if ($adapter->receive()) {
                        $oldPic = $aPostParams['oldpic'];
                        if (isset($oldPic) && !empty($oldPic) && file_exists($destinationPath . $oldPic)) {
                            unlink($destinationPath . $oldPic);
                        }

                        $aPostParams['userId'] = $userId;
                        $aPostParams['user_prp_pic'] = $ppName;
                        $iUserId = $oUsers->updateMyaccount($aPostParams, true);
                        $this->flashmessenger()->addMessage("Your image is successfully updated.");
                        $this->redirect()->toUrl('/profile/myaccount');
                    }
                }
            }
        } else {
            $this->redirect()->toUrl('/profile');
        }

        return $__viewVariables;
    }

}
