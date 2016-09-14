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
         $title = '';
         if ($this->getRequest()->isPost()){
             $aPostParams = $this->params()->fromPost('term');
             $title = $aPostParams;
         }else{
            echo 'console.log("No data posted")';
         }
         $listItem = $oVenueList->viewlist( $title);
         $__viewVariables['listItem'] = $listItem;
         $jsonvenueArr = array();
         if (!empty($listItem)) {
            foreach ($listItem as $vItem) {
                $jsonBrandArr[] = array('value' => $vItem['title']);
            }
        }
        $jsonvenue = json_encode($jsonBrandArr);
        echo $jsonvenue;
        
        exit();
        $__viewVariables['jsonvenue'] = $jsonvenue;
        
        return  $__viewVariables;
		
	}
    
}