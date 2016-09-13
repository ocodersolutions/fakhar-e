<?php
namespace Application\Controller;

use Zend\View\Model\ViewModel;
use Application\Model\Venue;
use Application\Model\VenueTable;
use Zend\Session\Container;
use Zend\Console\Request as ConsoleRequest;
use Ocoder\Base\BaseActionController;

class VenueController extends BaseActionController
{
	protected $_userid;
	protected $_oService;
    public function __construct(){
    }

	public function indexAction() 
	{
		 $__viewVariables = array();
		 $this->layout('layout/layout_elnove.phtml');

         $oVenueList = $this->getServiceLocator()->get('VenueTable');
         $isActive = 1;
         $listItem = $oVenueList->viewlist($isActive);
         $__viewVariables['listItem'] = $listItem;
        // $oAuth = $this->getServiceLocator()->get('AuthService');
        // if ($oAuth->hasIdentity() && $oAuth->getIdentity()->userType==2) {
        //     $this->layout()->showHeaderLinks = "LOGGED_IN";
        //     $userInfo = $oAuth->getIdentity();
        //     $this->userId = $userInfo->userId;
        //     $userId = $userInfo->userId;
        //     $userName = $userInfo->firstName;
        //     $this->layout()->firstName = $userInfo->firstName;
        //     $oStyleList = $this->getServiceLocator()->get('StyleListTable');
        //     $listItem = $oStyleList->viewlist($userId);
        //     $__viewVariables['listItem'] = $listItem;
        //     $__viewVariables['userName'] = $userName;
        // } else {
        //     $this->redirect()->toRoute('auth');
        // }
        
        return  $__viewVariables;
		//return new ViewModel(array(
             // 'VenueList' => $this->VenueTable()->fetchAll(),
        // ));
	}
    // public function definationAction() 
    // {
    //     $id= $this->params('id');
    //     $__viewVariables = array();
    //     $this->layout('layout/layout_elnove.phtml');
    //     $oAuth = $this->getServiceLocator()->get('AuthService');
    //     if ($oAuth->hasIdentity() && $oAuth->getIdentity()->userType==2) {
    //         $this->layout()->showHeaderLinks = "LOGGED_IN";
    //         $userInfo = $oAuth->getIdentity();
    //         $this->userId = $userInfo->userId;
    //         $this->layout()->firstName = $userInfo->firstName;
    //     } else {
    //         $this->redirect()->toRoute('auth');
    //     }

    //     return  $__viewVariables;
    // }
    // public function mystyleAction() {
    //     echo "12131";
    //     // die("success");
    //      $id= $this->params('id');
    //     var_dump($_REQUEST); 
        
    //     // $this->redirect()->toRoute('style/defination/2');
    // }
}