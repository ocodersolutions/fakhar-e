<?php
namespace Application\Controller;

use Zend\View\Model\ViewModel;
use Application\Model\Venue;
use Application\Model\VenueTable;
use Application\Model\VenueStyleTable;
use Zend\Session\Container;
use Zend\Console\Request as ConsoleRequest;
use Ocoder\Base\BaseActionController;
use Application\View\Helper\Getvenuehelper;

class VenueController extends BaseActionController
{
    public function __construct(){
    }

    public function indexAction() {
        $id = $this->params('id');
        $__viewVariables = array();
        $this->layout('layout/layout_elnove.phtml');
        $oVenueList = $this->getServiceLocator()->get('VenueTable');
        $ListItem = $oVenueList->getAllVenue(1);
        $listStyleV = $oVenueList->getVenueStyle($id);
        $listStyleArr = [];
        $ResultArr = [];
        if(!empty($listStyleV)){
            foreach ($listStyleV as $StyleV) {
                $listStyleArr[] = $StyleV['style_id'];
                
            }
            if(!empty($listStyleArr)){
                
                foreach ($listStyleArr as $StyleId) {
                    $oStyleList = $this->getServiceLocator()->get('StyleListTable');
                    $ListstyleItem = $oStyleList->viewsingleitem($StyleId);
                    if (!empty($ListstyleItem)){
                        $StyleName = $ListstyleItem->title;
                        //var_dump($StyleId);
                    }else{
                        $StyleName = "";
                    }
                    $oAttrofStyleList = $this->getServiceLocator()->get('StyleDefinationTable');
                    $oAttrofStyleArr =  $oAttrofStyleList->liststyle($StyleId);
                    //var_dump ( $oAttrofStyleArr);
                    foreach ($oAttrofStyleArr as $oAttrofStyle) {
                        //var_dump($oAttrofStyle);die;
                        $ResultArr[$StyleName][$oAttrofStyle['attribute']]= $oAttrofStyle['value'];
                        //$ResultArr[$StyleId][$oAttrofStyle['attribute']]= $oAttrofStyle['attribute'];
                    }
                    //var_dump($oAttrofStyle);
                    // $ResultArr['name'] = $StyleName;
                    // $ResultArr['name']['value'] = $oAttrofStyle;
                }
               
            }
        }
        $my_parent = $viewTitle = array();
        foreach( $ListItem as $item)
        {
            if (!empty($id)){
                 if($item['id'] == $id){
                $vName = $item['title'];
                // var_dump($vName);
                }
            }
            else{
                $vName = '';
            }
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
                $data_tree .= "{name:'" . $viewTitle[$key] . "',url: '/venue/".$key."',},";
            }
            else
            {
                $data_tree .= "{name:'" . $viewTitle[$key] . "',url: '/venue/".$key."',children: [";
                foreach ($value as $key_1 => $value_1) 
                {
                    if($value_1 == "")
                    {
                        $data_tree .= "{name:'" . $viewTitle[$key_1] . "',url: '/venue/".$key_1."',},";
                    }
                    else
                    {
                        $data_tree .= "{name:'" . $viewTitle[$key_1] . "',url: '/venue/".$key_1."',children: [";
                        foreach ($value_1 as $key_2 => $value_2) 
                        {
                            if($value_2 == "")
                            {
                                $data_tree .= "{name:'" . $viewTitle[$key_2] . "',url: '/venue/".$key_2."',},";
                            }
                            else
                            {

                            }
                        }
                        $data_tree .= "]},";
                    }
                }
                $data_tree .= "]},";
            }
        }
        $__viewVariables["venue_id"] = $id;
        $__viewVariables["venue_name"] = $vName;
        $__viewVariables["my_parent"] = $my_parent;
        $__viewVariables["ListItem"] = $ListItem;
        $__viewVariables["viewTitle"] = $viewTitle;
        $__viewVariables["data_tree"] = $data_tree;
        $__viewVariables["style_arr"] = $listStyleArr;
        $__viewVariables["style_attr"] =  $ResultArr;

        $oAttrList = $this->getServiceLocator()->get('StyleListTable');
        $attrItem = $oAttrList->viewlist(1);
        $arrayAttr = array();
        
        foreach($attrItem as $item){
            //var_dump($item);
            // if (!in_array($item['title'], $arrayAttr)){
                $arrayAttr[$item['id']]=$item['title'];
            // }
        }

        $__viewVariables['listAttrValue'] = $arrayAttr;

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
     public function savestylevenueAction(){
        // $id=$this->params('id');
        // var_dump($id);
        if ($this->getRequest()->isPost()){
            $styleid = $this->params()->fromPost('styleId');
            $venueid = $this->params()->fromPost('venueid');

            $data =array(
                'style_id'=>$styleid,
                'venue_id'=>$venueid
            );
            $oSavestyle = $this->getServiceLocator()->get('VenueStyleTable');
            $saveItem = $oSavestyle->insert($data);
            
         }
        return 0;

     }
}