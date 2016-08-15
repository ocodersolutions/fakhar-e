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
#use AcMailer\Service\MailService;

class EmailController extends AbstractActionController
{
    protected $_tableGateway; // default TableGateway variable
    

    private function getTable($tableName) {
        if (empty($this->_tableGateway)) {
            $this->_tableGateway = $this->getServiceLocator()->get($tableName);
        }
    }

  public function indexAction(){
    $oHelper = $this->getServiceLocator()->get('viewhelpermanager');
    $oService = $this->getServiceLocator();
    $outfitTable = $oService->get('OutfitsProductsTable');
    $outfitMissing = $outfitTable->getOutfitsProductsMissing();

    $configTable = $this->getTable('ConfigTable');

    $listUser = $this->_tableGateway->getUserEmailNotification()->toArray();


   $mailService = $this->sendMail();
   $layout = new \Zend\View\Model\ViewModel([
      'name' => 'John Doe',
      'date' => date('Y-m-d')
  ]);

  $layout->setTemplate('application/email/template',['charset' => 'utf-8']);

    $mailService->setTemplate($layout);
    $message = $mailService->getMessage();
    
    $message->addTo('tuanmythkt@gmail.com');
    $result = $mailService->send();

    if ($result->isValid()) {
      echo 'Message sent. Congratulations!';
    } else {
        if ($result->hasException()) {
            echo sprintf('An error occurred. Exception: \n %s', $result->getException()->getTraceAsString());
        } else {
            echo sprintf('An error occurred. Message: %s', $result->getMessage());
        }
    }
  }
}

