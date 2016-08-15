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

class ComingsoonController extends AbstractActionController
{
 
    public function indexAction()
    {
        try{
        $mailchimp = $this->getServiceLocator()->get('subscriber');
        $a = $mailchimp->email('fakharchughtai@hotmail.com')
                    ->listId('ade3c75987') 
                    ->emailType('html')
                    ->subscribe();
        } 
        catch (\Exception $e) {
            echo $e->getMessage();
        }
        
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
    
    public function HowItWorksAction()
    {
        $view = new ViewModel();
        $this->layout('layout/comingsoon_layout.phtml');
        $view->setTemplate('application/info/how-it-works.phtml');
        return $view;
    }
    
    public function aboutUsAction()
    {
        $view = new ViewModel();
        $this->layout('layout/comingsoon_layout.phtml');
        $view->setTemplate('application/info/about-us.phtml');
        return $view;        
    }

    public function frequentlyAskedQuestionsAction()
    {
        $view = new ViewModel();
        $this->layout('layout/comingsoon_layout.phtml');
        $view->setTemplate('application/info/frequently-asked-questions.phtml'); 
        return $view;
    }

    public function shippingAction()
    {
        $view = new ViewModel();
        $this->layout('layout/comingsoon_layout.phtml');
        $view->setTemplate('application/info/shipping.phtml'); 
        return $view;
    }   

    public function pricingAction()
    {
        $view = new ViewModel();
        $this->layout('layout/comingsoon_layout.phtml');
        $view->setTemplate('application/info/pricing.phtml'); 
        return $view;
    }

    public function exchangeAndReturnAction()
    {
        $view = new ViewModel();
        $this->layout('layout/comingsoon_layout.phtml');
        $view->setTemplate('application/info/exchange-and-return.phtml'); 
        return $view;
    }  

    public function careersAction()
    {
        $view = new ViewModel();
        $this->layout('layout/comingsoon_layout.phtml');
        $view->setTemplate('application/info/careers.phtml');
        return $view;
    }

    public function contactUsAction()
    {
        
        
        $aPostParams = $this->params()->fromPost();
        
        if (count($aPostParams)) {
            //print_r($aPostParams); die();
        
            $body = 'Hello Admin,<br/>A user send you a message via contact us form. His details are below:<br/><br/>';
            $body .= 'Name: '.$aPostParams['name'].'<br/>';
            $body .= 'Email: '.$aPostParams['email'].'<br/>';
            $body .= 'Subject: '.$aPostParams['subject'].'<br/>';
            $body .= 'Message: '.$aPostParams['message'].'<br/>';
        
            $htmlPart = new MimePart($body);
            $htmlPart->type = "text/html";
        
            $textPart = new MimePart($body);
            $textPart->type = "text/plain";
        
            $body = new MimeMessage();
            $body->setParts(array($textPart, $htmlPart));
        
            $options = new \Zend\Mail\Transport\SmtpOptions(array(
                'name' => 'elnove.com',
                'host' => 'smtp.zoho.com',
                'port' => 465,
                'connection_class' => 'login',
                'connection_config' => array(
                    'username' => 'no-reply@elnove.com',
                    'password' => '@aaf2014',
                    'ssl'      => 'ssl',
                ),
            ));
        
            $oTransport = new \Zend\Mail\Transport\Smtp();
            $oTransport->setOptions($options);
            $oMessage = new \Zend\Mail\Message();
            $oMessage->addFrom("no-reply@elnove.com", "Lnove Support");
            $oMessage->setTo("support@elnove.com");
            $oMessage->addReplyTo($aPostParams['email'], $aPostParams['name']);
            $oMessage->setSubject('A user send you a message via contact us form.');
            $oMessage->setEncoding("UTF-8");
            $oMessage->setBody($body);
            $oMessage->getHeaders()->get('content-type')->setType('multipart/alternative');
            $oTransport->send($oMessage);
        
            $this->redirect()->toUrl('/comingsoon/contact-us?success=1');
        }       
        
        $view = new ViewModel(
                array('postURL'=>'/comingsoon/contact-us')
            );
        $this->layout('layout/comingsoon_layout.phtml');
        $view->setTemplate('application/info/contact-us.phtml');
        return $view;
    }  

    public function privacyAndPoliciesAction()
    {
        $view = new ViewModel();
        $this->layout('layout/comingsoon_layout.phtml');
        $view->setTemplate('application/info/privacy-and-policies.phtml');
        return $view;
    }    
    
}
