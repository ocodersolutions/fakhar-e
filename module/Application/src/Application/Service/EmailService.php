<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Application\Service;

use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;
use Zend\Mail\Message;

class EmailService
{
	private $oTransport = null;
	private $oMessage = null;
	
    public function __construct()
    {
                $options   = new SmtpOptions(array(
                    'name'              => 'smtp.zoho.com',
                    'host'              => 'smtp.zoho.com',
                    'port'              => 465, // Notice port change for TLS is 587
                    'connection_class'  => 'login',
                    'connection_config' => array(
                        'username' => 'no-reply@elnove.com',
                        'password' => 'Kr3port!',
                        'ssl'      => 'SSL',
                    ),
                ));

        $this->oTransport = new SmtpTransport();
        $this->oTransport->setOptions($options);

        $this->oMessage = new Message();
        $this->oMessage->addFrom("no-reply@elnove.com", "ELNOVE Inc.");
        
        $this->body = new \Zend\Mime\Message();  

		return $this;
    }
    
    public function getMessageObj()
    {
    	return $this->oMessage;
    }
    
    public function send()
    {
        $this->oMessage->setBody( $this->body );
        try{
    	   $response = $this->oTransport->send( $this->oMessage );    	
    	}
    	catch(Zend\Mail\Exception\RuntimeException $ex)
    	{
    	    $ex->getMessage();
    	    $response = array('error' => 1, 'msg' => $ex->getMessage());
    	    //return $response;
    	}   
    	return $response; 	
    }
    
    public function setSubject( $sSubject )
    {
    	$this->oMessage->setSubject( $sSubject );
    	return $this;
    }    

    public function setFrom( $sEmail, $sName=null )
    {
    	$this->oMessage->setFrom( $sEmail, $sName );
    	return $this;
    }
        
    public function setTo( $sEmail )
    {
    	$this->oMessage->setTo( $sEmail );
    	return $this;
    }
    
    public function setBcc( $sEmail )
    {
    	$this->oMessage->setBcc( $sEmail );
    	return $this;
    }
    
    public function setCc( $sEmail )
    {
    	$this->oMessage->setCc( $sEmail );
    	return $this;
    }
    
    public function setReplyTo( $sEmail )
    {
    	$this->oMessage->setReplyTo( $sEmail );
    	return $this;
    }
        
    public function setHtmlBody( $sEmail )
    {
		$html = new \Zend\Mime\Part( $sEmail );
		$html->type = \Zend\Mime\Mime::TYPE_HTML;
		$html->disposition = \Zend\Mime\Mime::DISPOSITION_INLINE;
		$html->encoding = \Zend\Mime\Mime::ENCODING_QUOTEDPRINTABLE;
		$html->charset = 'utf-8'; 

	    $this->body->addPart($html);
		return $this;
    }    
    
    public function addAttachment( $sFile )
    {
        $attachment = new \Zend\Mime\Part( file_get_contents($sFile) );
        $attachment->type = \Zend\Mime\Mime::TYPE_OCTETSTREAM;
        $attachment->filename = basename($sFile);
        $attachment->disposition = \Zend\Mime\Mime::DISPOSITION_ATTACHMENT;

        $this->body->addPart($attachment);
        return $this;
    }    

    public function setTextBody( $sEmail )
    {
        $text = new \Zend\Mime\Part( $sEmail );
        $text->type = \Zend\Mime\Mime::TYPE_HTML;
        $text->disposition = \Zend\Mime\Mime::DISPOSITION_INLINE;
        $text->encoding = \Zend\Mime\Mime::ENCODING_QUOTEDPRINTABLE;
        $text->charset = 'utf-8';
    
        $this->body->addPart($text);
        return $this;
    }    
    
}
