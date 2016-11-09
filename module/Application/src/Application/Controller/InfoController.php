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
use Ocoder\Base\BaseActionController;

class InfoController extends BaseActionController
{
    public function placesAction()
    {
        
        $__viewVariables = array();
        $aPostParams = $this->params()->fromPost();
                
        if( isset($aPostParams['query']) ) {

            
            $plugin = $this->ReadXMLByNodePlugin();
            
            $url = "https://maps.googleapis.com/maps/api/place/textsearch/xml?key=AIzaSyBFdKVJAJCp50vbPOFJ6UoNdCn1ZGvC1hg&query=".urlencode($aPostParams['query']);
            $sContents = file_get_contents($url);
                        
            $plugin	->setFileContents( $sContents )->setParentNode('result');
            $res = $unq_types = array();
            while( $sNode = $plugin->getNodeFromContents() ) {
                 
                $res[] = array(
                    'name' => (string)$sNode->name,
                    'type' => (array)$sNode->type,
                    'formatted_address' => (string)$sNode->formatted_address,
                );
            
                $unq_types = array_merge($unq_types, (array)$sNode->type);
            }
            
            $__viewVariables['summary'] = array_unique($unq_types);
            $__viewVariables['result'] = $res;
            $__viewVariables['query'] = $aPostParams['query'];
        }

        $viewModel = new ViewModel( $__viewVariables );
        $viewModel->setTerminal(true);        
        return $viewModel;

    }
    
    public function howItWorksAction()
    {
        $this->layout('layout/layout_elnove.phtml');
    	$__viewVariables = array();
    	return $__viewVariables;
    }
    
    public function aboutUsAction()
    {
        $this->layout('layout/layout_elnove.phtml');
        $__viewVariables = array();
    	return $__viewVariables;
    }

    public function careersAction()
    {
        $this->layout('layout/layout_elnove.phtml');
        $__viewVariables = array();
        
        $aPostParams = $this->params()->fromPost();

        if (count($aPostParams)) {
            //Send email
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
        return $__viewVariables;
    }

    public function privacyAndPoliciesAction() {
        $this->layout('layout/layout_elnove.phtml');
        $__viewVariables = array();
        return $__viewVariables;
    }
	
	public function termsAndConditionsAction() {
        $this->layout('layout/layout_elnove.phtml');
        $__viewVariables = array();
        return $__viewVariables;
    }

    public function contactUsAction()
    {
        $aPostParams = $this->params()->fromPost();

        if (count($aPostParams)) {
            //Send Email
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
    	$this->layout('layout/layout_elnove.phtml');
    	return $__viewVariables;
    }    

    public function frequentlyAskedQuestionsAction()
    {
        $__viewVariables = array();
        $__viewVariables['queryParams'] = $this->params()->fromQuery();
        $this->layout('layout/layout_elnove.phtml');
    	return $__viewVariables;
    }

    public function shippingAction()
    {
        $__viewVariables = array();
    	$this->layout('layout/layout_elnove.phtml');
    	return $__viewVariables;
    }   

    public function pricingAction()
    {
        $__viewVariables = array();
    	$this->layout('layout/layout_elnove.phtml');
    	return $__viewVariables;
    }

    public function exchangeAndReturnAction()
    {
        $__viewVariables = array();
    	$this->layout('layout/layout_elnove.phtml');
    	return $__viewVariables;
    }    
}
