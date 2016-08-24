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
use Zend\Mail;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;

class InfoController extends AbstractActionController
{
    public function howItWorksAction()
    {
        $this->layout()->showSmallHeader = true;
        $this->layout()->showHeaderLinks = "NOT_LOGGED_IN";
        $oAuth = $this->getServiceLocator()->get('AuthService');
        if ($oAuth->hasIdentity()) {
            $this->layout()->showHeaderLinks = "LOGGED_IN";
            $userInfo = $oAuth->getIdentity();
            $this->layout()->firstName = $userInfo->firstName;
        }

    	$__viewVariables = array();
    	 
    	$this->layout('layout/home_layout.phtml');

    	return $__viewVariables;
    }
    
    public function aboutUsAction()
    {
        $this->layout()->showSmallHeader = true;
        $this->layout()->showHeaderLinks = "NOT_LOGGED_IN";
        $oAuth = $this->getServiceLocator()->get('AuthService');
        if ($oAuth->hasIdentity()) {
            $this->layout()->showHeaderLinks = "LOGGED_IN";
            $userInfo = $oAuth->getIdentity();
            $this->layout()->firstName = $userInfo->firstName;
        }

        $__viewVariables = array();
    
    	$this->layout('layout/home_layout.phtml');
    
    	return $__viewVariables;
    }

    public function careersAction()
    {
        $this->layout()->showSmallHeader = true;
        $this->layout()->showHeaderLinks = "NOT_LOGGED_IN";
        $oAuth = $this->getServiceLocator()->get('AuthService');
        if ($oAuth->hasIdentity()) {
            $this->layout()->showHeaderLinks = "LOGGED_IN";
            $userInfo = $oAuth->getIdentity();
            $this->layout()->firstName = $userInfo->firstName;
        }

        $__viewVariables = array();
        
        $aPostParams = $this->params()->fromPost();
        
        if (count($aPostParams)) {
            //print_r($aPostParams); die();
        
            $body = 'Hello Admin,<br/>A user send you a message via "LNove Careers". Details are below:<br/><br/>';
            $body .= 'Name: '.$aPostParams['name'].'<br/>';
            $body .= 'Email: '.$aPostParams['email'].'<br/>';
            $body .= 'Message: '.$aPostParams['message'].'<br/>';
            
            $oEmailService = $this->getServiceLocator()->get('Application\Service\EmailService');
            $oEmailService->setTo('fakharchughtai@gmail.com');
            $oEmailService->setSubject( 'LNOVE - Careers' );
            $oEmailService->setHtmlBody( $body );
            
            if( isset($_FILES['subject']['name']) ) {
               
                $size = new \Zend\Validator\File\Size(array('max'=>6000000)); //minimum bytes filesize
                $adapter = new \Zend\File\Transfer\Adapter\Http();
                $adapter->setValidators(array($size), $_FILES['subject']['name']);
                if (!$adapter->isValid()){
                    $dataError = $adapter->getMessages();
                    $error = array();
                    foreach($dataError as $key=>$row)
                    {
                        $error[] = $row;
                    } //set formElementErrors
                } else {
                    $adapter->setDestination('/tmp/');
                    if ($adapter->receive($_FILES['subject']['name'])) {
                        $newfile = $adapter->getFileName(); 
                        $oEmailService->addAttachment( $newfile );
                    }
                }
            }
            $oEmailService->send();          

            $this->redirect()->toUrl('/info/careers?success=1');
        }        

       $this->layout('layout/layout_elnove.phtml');

        return $__viewVariables;
    }
    public function privacyAndPoliciesAction()
    {
        $this->layout()->showSmallHeader = true;
        $this->layout()->showHeaderLinks = "NOT_LOGGED_IN";
        $oAuth = $this->getServiceLocator()->get('AuthService');
        if ($oAuth->hasIdentity()) {
            $this->layout()->showHeaderLinks = "LOGGED_IN";
            $userInfo = $oAuth->getIdentity();
            $this->layout()->firstName = $userInfo->firstName;
        }

        $__viewVariables = array();

        $this->layout('layout/home_layout.phtml');

        return $__viewVariables;
    }
	
	public function termsAndConditionsAction()
   {
        $this->layout()->showSmallHeader = true;
        $this->layout()->showHeaderLinks = "NOT_LOGGED_IN";
        $oAuth = $this->getServiceLocator()->get('AuthService');
        if ($oAuth->hasIdentity()) {
            $this->layout()->showHeaderLinks = "LOGGED_IN";
            $userInfo = $oAuth->getIdentity();
            $this->layout()->firstName = $userInfo->firstName;
        }

        $__viewVariables = array();

        $this->layout('layout/home_layout.phtml');

        return $__viewVariables;
    }

    public function contactUsAction()
    {
        $this->layout()->showSmallHeader = true;
        $this->layout()->showHeaderLinks = "NOT_LOGGED_IN";
        $oAuth = $this->getServiceLocator()->get('AuthService');
        if ($oAuth->hasIdentity()) {
            $this->layout()->showHeaderLinks = "LOGGED_IN";
            $userInfo = $oAuth->getIdentity();
            $this->layout()->firstName = $userInfo->firstName;
        }

        $aPostParams = $this->params()->fromPost();

        if (count($aPostParams)) {
            //print_r($aPostParams); die();

            $body = 'Hello Admin,<br/>A user send you a message via contact us form. His details are below:<br/><br/>';
            $body .= 'Name: '.$aPostParams['name'].'<br/>';
            $body .= 'Email: '.$aPostParams['email'].'<br/>';
            $body .= 'Subject: '.$aPostParams['subject'].'<br/>';
            $body .= 'Message: '.$aPostParams['message'].'<br/>';

            $oEmailService = $this->getServiceLocator()->get('Application\Service\EmailService');
            $oEmailService->setTo('support@elnove.com');
            $oEmailService->setSubject( 'LNOVE - Contact Us' );
            $oEmailService->setHtmlBody( $body );
            $oEmailService->send();            
            
            $this->redirect()->toUrl('/info/contact-us?success=1');
        }

        $__viewVariables = array();
    
    	$this->layout('layout/home_layout.phtml');
    
    	return $__viewVariables;
    }    

    public function frequentlyAskedQuestionsAction()
    {
        $this->layout()->showSmallHeader = true;
        $this->layout()->showHeaderLinks = "NOT_LOGGED_IN";
        $oAuth = $this->getServiceLocator()->get('AuthService');
        if ($oAuth->hasIdentity()) {
            $this->layout()->showHeaderLinks = "LOGGED_IN";
            $userInfo = $oAuth->getIdentity();
            $this->layout()->firstName = $userInfo->firstName;
        }

        $__viewVariables = array();
    
        $__viewVariables['queryParams'] = $this->params()->fromQuery();
        
    	$this->layout('layout/home_layout.phtml');
    
    	return $__viewVariables;
    }

    public function shippingAction()
    {
        $this->layout()->showSmallHeader = true;
        $this->layout()->showHeaderLinks = "NOT_LOGGED_IN";
        $oAuth = $this->getServiceLocator()->get('AuthService');
        if ($oAuth->hasIdentity()) {
            $this->layout()->showHeaderLinks = "LOGGED_IN";
            $userInfo = $oAuth->getIdentity();
            $this->layout()->firstName = $userInfo->firstName;
        }

        $__viewVariables = array();
    
    	$this->layout('layout/home_layout.phtml');
    
    	return $__viewVariables;
    }   

    public function pricingAction()
    {
        $this->layout()->showSmallHeader = true;
        $this->layout()->showHeaderLinks = "NOT_LOGGED_IN";
        $oAuth = $this->getServiceLocator()->get('AuthService');
        if ($oAuth->hasIdentity()) {
            $this->layout()->showHeaderLinks = "LOGGED_IN";
            $userInfo = $oAuth->getIdentity();
            $this->layout()->firstName = $userInfo->firstName;
        }

        $__viewVariables = array();
    
    	$this->layout('layout/home_layout.phtml');
    
    	return $__viewVariables;
    }

    public function exchangeAndReturnAction()
    {
        $this->layout()->showSmallHeader = true;
        $this->layout()->showHeaderLinks = "NOT_LOGGED_IN";
        $oAuth = $this->getServiceLocator()->get('AuthService');
        if ($oAuth->hasIdentity()) {
            $this->layout()->showHeaderLinks = "LOGGED_IN";
            $userInfo = $oAuth->getIdentity();
            $this->layout()->firstName = $userInfo->firstName;
        }

        $__viewVariables = array();
    
    	$this->layout('layout/home_layout.phtml');
    
    	return $__viewVariables;
    }    
}
