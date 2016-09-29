<?php
namespace Application\Controller;

use Zend\Validator\Db\RecordExists;
use Zend\Validator\Db\NoRecordExists;
use Zend\View\Model\ViewModel;
use Application\Model\Venue;
use Application\Model\VenueTable;
use Application\Model\VenueStyleTable;
use Zend\Session\Container;
use Zend\Console\Request as ConsoleRequest;
use Ocoder\Base\BaseActionController;
use Application\View\Helper\Getvenuehelper;
use Zend\Json\Json;
class VenueController extends BaseActionController
{
    public function __construct(){
    }

    public function indexAction() {
       
        $id = $this->params('id');
        $__viewVariables = array();
        $this->layout('layout/layout_elnove.phtml');

        $oAuth = $this->getServiceLocator()->get('AuthService');
        if ($oAuth->hasIdentity() && $oAuth->getIdentity()->userType==2) 
        {

            $oVenueList = $this->getServiceLocator()->get('VenueTable');
            $oStyleList = $this->getServiceLocator()->get('StyleListTable');
            $oAttrofStyleList = $this->getServiceLocator()->get('StyleDefinationTable');
            $ListItem = $oVenueList->getAllVenue(1);
            $listStyleV = $oVenueList->getVenueStyle($id);
            $listStyleArr = [];
            $ResultArr = [];
            $StyleName = "";
            $parentStArr = [];
            if(!empty($listStyleV)){
                foreach ($listStyleV as $StyleV) {
                    $listStyleArr[] = $StyleV['style_id'];

                }
                if(!empty($listStyleArr)){

                    foreach ($listStyleArr as $StyleId) {
                        $StyleNameArr = $oStyleList ->viewsingleitem($StyleId);
                        $StyleName = $StyleNameArr ->title;
                        $oAttrofStyleArr =  $oAttrofStyleList->liststyle($StyleId);
                        foreach ($oAttrofStyleArr as $oAttrofStyle) {
                            $ResultArr[$StyleId][$StyleName][$oAttrofStyle['attribute']]= $oAttrofStyle['value'];
                        }
                        
                    }
                }
            }
            $my_parent = $viewTitle = array();
            foreach( $ListItem as $item)
            {
                if (!empty($id)){
                     if($item['id'] == $id){
                    $vName = $item['title'];
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
            $childArr = [];
            $ResultChild = [];
            $GrandChildArr=[];
            $parentArr = [];
            $resultparentArr=[];
            $GrandParentArr=[];
            $resultGrparentArr=[];
            $resultChildArr=[];
            foreach ($my_parent as $key => $value) 
            {
                if($key == $id)
                {
                   $childArr[$viewTitle[$key]] = $value;
                }
                 if($value == "")
                {
                    $data_tree .= "{name:'" . $viewTitle[$key] . "',url: '/venue/".$key."',},";
                }
                else
                {
                    $data_tree .= "{name:'" . $viewTitle[$key] . "',url: '/venue/".$key."',children: [";
                    foreach ($value as $key_1 => $value_1) 
                    {   
                        if($key_1 == $id)
                        {
                            $GrandParentArr=[];
                            $parentArr[$key] = '';
                            $childArr[$viewTitle[$key_1]] = $value_1;
                        }

                        if($value_1 == "")
                        {
                            $data_tree .= "{name:'" . $viewTitle[$key_1] . "',url: '/venue/".$key_1."',},";
                        }
                        else
                        {
                            $data_tree .= "{name:'" . $viewTitle[$key_1] . "',url: '/venue/".$key_1."',children: [";
                            foreach ($value_1 as $key_2 => $value_2) 
                            {
                                 if($key_2 == $id)
                                {
                                    $GrandParentArr[$key]='';
                                    $parentArr[$key_1] ='' ;
                                    $childArr[$viewTitle[$key_2]] = $value_2;
                                }
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
            foreach ($childArr as $current => $Child) {
                if(is_array($Child)){
                    foreach ($Child as $ChildId => $valueChild) { // work office-office (7) or work1 (14)
                        $nameV =$oVenueList ->getnameVenue($ChildId);
                        $ResultChild[$nameV]= '';
                        $listStyChild = $oVenueList->getVenueStyle($ChildId);
                        foreach ($listStyChild as $StyChildId => $StyChild_value) { 

                            $StyChild_id = $StyChild_value['style_id'];
                            $nameStyChild =$oStyleList->viewsingleitem($StyChild_id);
                            $ResultChild[$nameV][$nameStyChild->title]='';
                            $ListAttofChild=$oAttrofStyleList->liststyle($StyChild_id);
                            if(!empty($ListAttofChild)){
                                foreach ($ListAttofChild as $Attrkey => $Attrvalue) {
                                    $ResultChild[$nameV][$nameStyChild->title][$Attrvalue['attribute']]=$Attrvalue['value'];
                                }
                            }
                        }
                        if(!empty($valueChild)){
                            foreach ($valueChild as $GranchildId => $Granchildvalue) { 
                                $GrannameV = $oVenueList ->getnameVenue($GranchildId);
                                $GrandChildArr[$GrannameV]='';
                                $GranchildStyle = $oVenueList->getVenueStyle($GranchildId);
                                foreach ($GranchildStyle as $arrId) {
                                    $GrandChilId = $arrId['style_id'];
                                    $nameStyGrandChild =$oStyleList->viewsingleitem($GrandChilId);
                                    $StyGrandChildname=$nameStyGrandChild->title;
                                    $GrandChildArr[$GrannameV][$StyGrandChildname]='';
                                    $ListAttofGrandChild=$oAttrofStyleList->liststyle($GrandChilId);
                                    foreach ($ListAttofGrandChild as $key => $value) {
                                        $GrandChildArr[$GrannameV][$StyGrandChildname][$value['attribute']]=$value['value'];
                                    }
                                }
                                
                            }
                        }
                    }
                }else{
                    $ResultChild=[];
                }
            }

            //parent array
            
                foreach ($parentArr as $Pid => $Pvalue) {
                    $PrnameV =$oVenueList ->getnameVenue($Pid);
                    $resultparentArr[$PrnameV] = '';
                    $listStyParent = $oVenueList->getVenueStyle($Pid);
                        foreach ($listStyParent as $StParentId) {
                            $StParentId = $StParentId['style_id'];
                            $nameStyParentList =$oStyleList->viewsingleitem($StParentId);
                            $nameStyParent=$nameStyParentList->title;
                            $resultparentArr[$PrnameV][$nameStyParent]='';
                            $ListAttofparent=$oAttrofStyleList->liststyle($StParentId);
                            foreach ($ListAttofparent as $Attofparentkey => $Attofparentvalue) {
                              $resultparentArr[$PrnameV][$nameStyParent][$Attofparentvalue['attribute']] = $Attofparentvalue['value'];
                            }
                        }
                    }
                    //var_dump($resultparentArr);
            // grand parent array

                foreach ($GrandParentArr as $GrPeid => $GrPvalue) {
                    $GrPrnameV =$oVenueList ->getnameVenue($GrPeid);
                    $listStyGrParent = $oVenueList->getVenueStyle($GrPeid);
                    foreach ($listStyGrParent as $StGrParentId) {
                        $StGrParent_Id = $StGrParentId['style_id'];
                        $nameStyGrParentList =$oStyleList->viewsingleitem($StGrParent_Id);
                        $nameStyGrParent=$nameStyGrParentList->title;
                        $ListAttofGrparent=$oAttrofStyleList->liststyle($StGrParent_Id);
                        $resultGrparentArr[$GrPrnameV][$nameStyGrParent]='';
                        
                        
                        foreach ($ListAttofGrparent as $AttofGrparentkey => $AttofGrparentvalue) {
                            $resultGrparentArr[$GrPrnameV][$nameStyGrParent][$AttofGrparentvalue['attribute']]=$AttofGrparentvalue['value'];
                        }
                    }
               }

            $__viewVariables["venue_id"] = $id;
            $__viewVariables["venue_name"] = $vName;
            $__viewVariables["my_parent"] = $my_parent;
            $__viewVariables["viewTitle"] = $viewTitle;
            $__viewVariables["data_tree"] = $data_tree;
            $__viewVariables["style_arr"] = $listStyleArr;
            $__viewVariables["style_attr"] =  $ResultArr;
            $__viewVariables["child_arr"] =  $ResultChild;
            $__viewVariables["GrandChild_Arr"] =  $GrandChildArr;
            $__viewVariables["Parent_Arr"] =  $resultparentArr;
            $__viewVariables["GrandParent_Arr"] =  $resultGrparentArr;

            $oAttrList = $this->getServiceLocator()->get('StyleListTable');
            $attrItem = $oAttrList->viewlist(1);
            $arrayAttr = array();
            foreach($attrItem as $item){
                   $arrayAttr[$item['id']]=$item['title'];
            }

            $__viewVariables['listAttrValue'] = $arrayAttr;
        }else{
             $this->redirect()->toRoute('auth');
        }
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
        $__viewVariables = array();
        $this->layout('layout/layout_elnove.phtml');
        if ($this->getRequest()->isPost()){

            $styleid = $this->params()->fromPost('styleId');
            $venueid = $this->params()->fromPost('venueid');
            $data =array(
                'style_id'=>$styleid,
                'venue_id'=>$venueid
            );
            $oSavestyle = $this->getServiceLocator()->get('VenueStyleTable');


            $validator = new RecordExists(
                array(
                    'table'   => 'venuestyle',
                    'field'   => 'style_id',
                    'adapter' => $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'),
                    'exclude' => ' venue_id = '.$venueid
                )
            );
            $validator->isValid($styleid) ? $recordExists = 1 : $recordExists = 0;
            if($recordExists == 0){
                $saveItem = $oSavestyle->AddVenue($data);
            }else{
                $saveItem = "style already exits";
            }
        }
        return $this->getResponse()->setContent(Json::encode($saveItem));

        $oStyleList = $this->getServiceLocator()->get('StyleListTable');
        $StyleArr = $oStyleList->viewsingleitem($styleid);
        $name = $StyleArr->title;
        $oAttrofStyleList = $this->getServiceLocator()->get('StyleDefinationTable');
        $style = $oAttrofStyleList->liststyle($styleid);
        $ResultoView = [];
        foreach ($style as $key => $value) {
                    $ResultoView[$value['attribute']]= $value['value'];
                } 
         $__viewVariables['name_style_add'] =  $name; 
         $__viewVariables['ResultoView'] =   $ResultoView;
        return $this->getResponse()->setContent(Json::encode($ResultoView));          
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