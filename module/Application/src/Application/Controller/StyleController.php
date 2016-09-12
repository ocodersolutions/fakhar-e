<?php
namespace Application\Controller;

use Zend\View\Model\ViewModel;
use Application\Model\FeedDataTable;
use Application\Model\StyleListTable;
use Zend\Session\Container;
use Zend\Console\Request as ConsoleRequest;
use Ocoder\Base\BaseActionController;

class StyleController extends BaseActionController
{
	protected $_userid;
	protected $_oService;
    public function __construct(){
    }

	public function indexAction() 
	{
		$__viewVariables = array();
		$this->layout('layout/layout_elnove.phtml');
        $oAuth = $this->getServiceLocator()->get('AuthService');
        if ($oAuth->hasIdentity() && $oAuth->getIdentity()->userType==2) {
            $this->layout()->showHeaderLinks = "LOGGED_IN";
            $userInfo = $oAuth->getIdentity();
            $this->userId = $userInfo->userId;
            $userId = $userInfo->userId;
            $userName = $userInfo->firstName;
            $this->layout()->firstName = $userInfo->firstName;
        } else {
            $this->redirect()->toRoute('auth');
        }
        $oStyleList = $this->getServiceLocator()->get('StyleListTable');
        $listItem = $oStyleList->viewlist($userId);
        $__viewVariables['listItem'] = $listItem;
        $__viewVariables['userName'] = $userName;

		return 	$__viewVariables;
	}
    public function definationAction() 
    {
        $id= $this->params('id');
        $__viewVariables = array();
        $this->layout('layout/layout_elnove.phtml');
        $oAuth = $this->getServiceLocator()->get('AuthService');
        if ($oAuth->hasIdentity() && $oAuth->getIdentity()->userType==2) {
            $this->layout()->showHeaderLinks = "LOGGED_IN";
            $userInfo = $oAuth->getIdentity();
            $this->userId = $userInfo->userId;
            $this->layout()->firstName = $userInfo->firstName;
        } else {
            $this->redirect()->toRoute('auth');
        }

        return  $__viewVariables;
    }
    public function mystyleAction() {
        echo "12131";
        die("success");
        // $this->redirect()->toRoute('style/defination');
    }
}