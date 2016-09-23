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
        $StyleName = "";
        if(!empty($listStyleV)){
            foreach ($listStyleV as $StyleV) {
                $listStyleArr[] = $StyleV['style_id'];
                
            }
            if(!empty($listStyleArr)){
                
                foreach ($listStyleArr as $StyleId) {
                    $oStyleList = $this->getServiceLocator()->get('StyleListTable');
                    $ListstyleItem = $oStyleList->viewsingleitem($StyleId);
                    //var_dump($ListstyleItem);
                   
                        $StyleName = $ListstyleItem->title;
                        $StyleId = $ListstyleItem ->id;
                        //var_dump($StyleId);
                    // foreach ($ListstyleItem as $key => $value) {
                    //     # code...
                    // }
                     
                  
                    $oAttrofStyleList = $this->getServiceLocator()->get('StyleDefinationTable');
                    $oAttrofStyleArr =  $oAttrofStyleList->liststyle($StyleId);
                    //var_dump ( $oAttrofStyleArr);
                    foreach ($oAttrofStyleArr as $oAttrofStyle) {
                        //var_dump($oAttrofStyle);
                        $ResultArr[$StyleId][$StyleName][$oAttrofStyle['attribute']]= $oAttrofStyle['value'];
                        //$ResultArr[$StyleId][$oAttrofStyle['attribute']]= $oAttrofStyle['attribute'];
                    }
                    
                    // $ResultArr['name'] = $StyleName;
                    // $ResultArr['name']['value'] = $oAttrofStyle;
                }
                 //var_dump($ResultArr);
            }
            //var_dump($ResultArr);
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
        $jsonArr = $arrFind = array();
        if ($listItem != ''){
           foreach ($listItem as $item) {
               $jsonArr[$item["id"]]= $item['title'];
               $arrFind[$item["title"]] = 0;
            }            
        }
        $aPostParams = $this->params()->fromPost(); 
        $data_imp = $aPostParams['data_form'];
        $data_imp = str_replace("+", " ", trim($data_imp));
        $result = "error";
        foreach ($jsonArr as $key => $value) {
            if(strtolower($data_imp) == strtolower($value)) {   $result = $value; break; }
            else
            {
                $data_imp_2 = strtolower(str_replace(" ", "", $data_imp));
                $length_data = strlen($data_imp_2);
                $length_value = strlen(str_replace(" ", "", $value));
                $value_2 = strtolower(trim($value));
                $value_2 = str_replace(" ", "", $value_2);
                for($i = 1; $i <= $length_data; $i++)
                {
                    $length_index = "";
                    if($length_data > $length_value)
                    {
                        $length_index = $length_value;
                    }
                    else
                    {
                        $length_index = $length_data;
                    }
                    for($j = 1; $j <= $length_index; $j++)
                    {
                        $sub_string = substr($data_imp_2, $i-1, $j);
                        $arrFind[$value] += substr_count($value_2, $sub_string);
                    }
                }
            }
        }
        $five_max = array();
        $max = 0;
        foreach ($arrFind as $key => $value) {
            $five_max[] = $value . "-" . $key;
        }
        rsort($five_max);        

        foreach ($five_max as $key => $value) {
            $temporary = explode("-", $value);
            $five_max[$key] = $temporary[1];
        }

        $result_2 = "<div>" . $five_max[0] . "</div> <div>" . $five_max[1] . "</div> <div>" . $five_max[2] . "</div> <div>" . $five_max[3] . "</div> <div>" . $five_max[4] . "</div>";
        if($result == "error" ) 
        {
            $result_post = json_encode(array('status1'=>$result, 'max_1'=>$result_2));
        }
        else
        {
            $result_post = json_encode(array('status1'=>$result));
        }
        echo $result_post; exit(0);

        return  $__viewVariables;
    }

     public function savestylevenueAction(){
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
     public function delstylevenueAction(){
        
         if ($this->getRequest()->isPost()){
            $style_id = $this->params()->fromPost('delstId');
            $venue_id = $this->params()->fromPost('venueId');
            
            $oDelstyle = $this->getServiceLocator()->get('VenueStyleTable');
            $DelItem = $oDelstyle->delete($style_id,$venue_id);
            
         }
         return  $DelItem;
     }
}