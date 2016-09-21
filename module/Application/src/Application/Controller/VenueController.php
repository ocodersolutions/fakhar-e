<?php
namespace Application\Controller;

use Zend\View\Model\ViewModel;
use Application\Model\Venue;
use Application\Model\VenueTable;
use Zend\Session\Container;
use Zend\Console\Request as ConsoleRequest;
use Ocoder\Base\BaseActionController;
use Application\View\Helper\Getvenuehelper;

class VenueController extends BaseActionController
{
    public function __construct(){
    }

    public function indexAction() {
        $__viewVariables = array();
        $this->layout('layout/layout_elnove.phtml');
        $oVenueList = $this->getServiceLocator()->get('VenueTable');
        $ListItem = $oVenueList->getAllVenue(1);
        $my_parent = $viewTitle = array();
        foreach( $ListItem as $item)
        {
            $viewTitle[$item["id"]] = $item["title"];
            if($item["parentId"] == 0 )
            {
                $my_parent[$item["id"]] = "";
            }
        }
        foreach ($my_parent as $key_item => $value_item) 
        {
            foreach($ListItem as $sub_item)
            {
                if($sub_item["parentId"] == $key_item)
                {
                    $my_parent[$key_item][$sub_item["id"]] = "";
                }
            }
        }
        foreach ($my_parent as $key_item => $value_item) 
        {
            if($value_item !== "")
            {
                foreach($value_item as $key_sub_item => $value_sub_item)
                {
                    foreach($ListItem as $sub_item)
                    {
                        if($sub_item["parentId"] == $key_sub_item)
                        {
                            $my_parent[$key_item][$key_sub_item][$sub_item["id"]] = "";
                        }
                    }
                }
            }
        }
        $data_tree = "";
        foreach ($my_parent as $key => $value) 
        {
            if($value == "")
            {
                $data_tree .= "{name:'" . $viewTitle[$key] . "',url: '/venue/" . $key . "',},";
            }
            else
            {
                $data_tree .= "{name:'" . $viewTitle[$key] . "',url: '/venue/" . $key . "',children: [";
                foreach ($value as $key_1 => $value_1) 
                {
                    if($value_1 == "")
                    {
                        $data_tree .= "{name:'" . $viewTitle[$key_1] . "',url: '/venue/" . $key_1 . "',},";
                    }
                    else
                    {
                        $data_tree .= "{name:'" . $viewTitle[$key_1] . "',url:'/venue/" . $key_1 . "',children: [";
                        foreach ($value_1 as $key_2 => $value_2) 
                        {
                            if($value_2 == "")
                            {
                                $data_tree .= "{name:'" . $viewTitle[$key_2] . "',url: '/venue/" . $key_2 . "',},";
                            }
                        }
                        $data_tree .= "]},";
                    }
                }
                $data_tree .= "]},";
            }
        }

        $__viewVariables["my_parent"] = $my_parent;
        $__viewVariables["ListItem"] = $ListItem;
        $__viewVariables["viewTitle"] = $viewTitle;
        $__viewVariables["data_tree"] = $data_tree;

        return  $__viewVariables;
    }


    public function venueautocompleAction() 
    {
        
         $__viewVariables = array();
         $this->layout('layout/layout_elnove.phtml');
         $oVenueList = $this->getServiceLocator()->get('VenueTable');
         $title = '';
         
         if ($this->getRequest()->isPost()){
             $aPostParams = $this->params()->fromPost('term');
             $title = $aPostParams;

         }
         $listItem = $oVenueList->viewlist( $title);
         $__viewVariables['listItem'] = $listItem;
         $jsonvenueArr = array();
         if (!empty($listItem)) {
            foreach ($listItem as $vItem) {
                 $jsonvenueArr[] =  $vItem['title'];
            }
        }
        $jsonvenue = json_encode($jsonvenueArr);
        echo $jsonvenue;
        
        exit();
        $__viewVariables['jsonvenue'] = $jsonvenue;
        
        return  $__viewVariables;
        
    }
     public function searchAction() 
    {
        $__viewVariables = array();
        $this->layout('layout/layout_elnove.phtml');
        $oVenueList = $this->getServiceLocator()->get('VenueTable');
        $isActive = 1;
        $listItem = $oVenueList->getAllVenue($isActive);
        $jsonArr = array();
        if ($listItem != ''){
           foreach ($listItem as $item) {
               $jsonArr[]= $item['title'];
            }
            
        }
         // $s = "cawors";
         // $s1 = "casual";
        //  $help = new Getvenuehelper;
        // $kq =$help->comparestring($s,$s1);
        
        $jsonvenue = json_encode($jsonArr);
        echo $jsonvenue;
        exit();
        // $__viewVariables['comparers'] =$kq ;
        return  $__viewVariables;
    }

}