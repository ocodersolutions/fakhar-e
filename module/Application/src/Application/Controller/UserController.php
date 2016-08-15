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

class UserController extends AbstractActionController
{
    private $secretKey = 'AUvSAfcDTwEu6GIcBYtYUFQGX6nfWEVebeerSWpIkx13K';
    
    public function indexAction()
    {
        return new ViewModel();
    }
    
    public function checkAccountAction()
    {
    	$oUsers = $this->getServiceLocator()->get('UsersTable');
    	$aPostParams = $this->params()->fromPost();
    	$bStatus =  true;
    	if( count($aPostParams) ) {
    		$bStatus = $oUsers->isUnique( 'email', $aPostParams['email'] );
    	}
        echo ( $bStatus ? 'true' : 'false' );
    	exit(0);
    }

    public function changepasswordAction()
    {
		$__viewVariables = array();

        $oAuth = $this->getServiceLocator()->get('AuthService');
        if( $oAuth->hasIdentity() ) {
            $oUserProfile = $this->getServiceLocator()->get('UserProfilesTable');
            $oUserProfile->updateUserProfile( $oAuth->getIdentity()->userId, $this->oSession->data );
            $this->redirect()->toUrl( '/service' );
        }

        $aPostParams = $this->params()->fromPost();

        if( count($aPostParams) ) {

            $oUsers = $this->getServiceLocator()->get('UsersTable');
            $aPostParams = $this->params()->fromPost();

            $bStatus =  true;
            if( count($aPostParams) ) {
               
                $cipher = BlockCipher::factory('mcrypt',  array(
                                'algo' => 'blowfish',
                                'mode' => 'cfb',
                                'hash' => 'sha512'
                            ));
                $cipher->setKey( $this->secretKey );

                $aPostParams['email']  = $cipher->decrypt( str_replace('-','/',$aPostParams['email'] ));
            
                //$aPostParams['email'] = base64_decode($aPostParams['email']);
                $bStatus = !$oUsers->isUnique( 'email', $aPostParams['email']);
            }

            if( $bStatus == true ) {
                $iUserId = $oUsers->getUserByEmail('email', $aPostParams['email']);
                $insertData = array();
                $insertData['password'] = $aPostParams['password'];
                $insertData['userId'] = $iUserId;
                $oUsers->updatePassword( $insertData );
				//Send email 
				$this->renderer = $this->getServiceLocator()->get('ViewRenderer');
				$user_info =  $oUsers->getUserInfo('email',$aPostParams['email']);
				$layout = new ViewModel([
					'user_info'		=> $user_info
				]);
				$layout->setTemplate('application/email/reset');
				
				$body = $this->renderer->render($layout);
				//echo $body; die;

				$oEmailService = $this->getServiceLocator()->get('Application\Service\EmailService');
				$oEmailService->setTo($aPostParams['email']);
				$oEmailService->setSubject('Your password on elnove has been reset.');
				$oEmailService->setHtmlBody( $body );
				$oEmailService->send(); 
							
                $this->redirect()->toUrl( '/auth/login' );
            }

        }
        else if($this->params('param1') != '')
        {
            $view = new ViewModel();
            $view->encryptemail = $this->params('param1');
            return $view;
        }
        else
        {
            die('Hacking Attempt.');
        }

    }
}
