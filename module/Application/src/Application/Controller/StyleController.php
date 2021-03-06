<?php
namespace Application\Controller;
use Zend\Validator\Db\RecordExists;
use Zend\Validator\Db\NoRecordExists;
use Zend\View\Model\ViewModel;
use Application\Model\FeedDataTable;
use Application\Model\StyleListTable;
use Application\Model\AttributeListTable;
use Application\Model\Table;
use Zend\Session\Container;
use Zend\Console\Request as ConsoleRequest;
use Ocoder\Base\BaseActionController;
use Zend\Json\Json;

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
            $oStyleList = $this->getServiceLocator()->get('StyleListTable');
            $listItem = $oStyleList->viewlistall();
            $__viewVariables['listItem'] = $listItem;
            $__viewVariables['userName'] = $userName;

            $aPostParams = $this->params()->fromPost();
            if(array_key_exists('submit', $aPostParams) && $aPostParams['submit'] == "Create") {
                $result = $oStyleList->insert($aPostParams);
                $this->redirect()->toRoute('style/default', array('action' => 'defination', 'id' => $result));    
            }
        } else {
            $this->redirect()->toRoute('auth');
        }
		return 	$__viewVariables;
	}
    public function getAttributeValueAction() 
    {
        $aPostParams = $this->params()->fromPost();
        $value = $aPostParams['selected'];
        $oAttrList = $this->getServiceLocator()->get('AttributeListTable');
        $attrItem = $oAttrList->getAttributeValue($value);
        $listItem = array();
        foreach($attrItem as $item){
            $listItem[] = $item['attribute_value'];
        }
        return $this->getResponse()->setContent(Json::encode($listItem));
    }
    public function definationAction() 
    {
        $id = $this->params('id');
        $__viewVariables = array();
        $this->layout('layout/layout_elnove.phtml');
        $oAuth = $this->getServiceLocator()->get('AuthService');
        if ($oAuth->hasIdentity() && $oAuth->getIdentity()->userType==2) {
            $this->layout()->showHeaderLinks = "LOGGED_IN";
            $userInfo = $oAuth->getIdentity();
            $this->userId = $userInfo->userId;
            $userId = $userInfo->userId;
            $this->layout()->firstName = $userInfo->firstName;
        } else {
            $this->redirect()->toRoute('auth');
        }

        $oAttrList = $this->getServiceLocator()->get('AttributeListTable');
        $attrItem = $oAttrList->getAttributeName();
        $arrayAttr = array();
        
        foreach($attrItem as $item){
            if (!in_array($item['attribute_name'], $arrayAttr)){
                array_push($arrayAttr,$item['attribute_name']);
            }
        }
        $__viewVariables['listAttrValue'] = $arrayAttr;
        $oStyleList = $this->getServiceLocator()->get('StyleListTable');
        $aPostParams = $this->params()->fromPost();
        if(isset($aPostParams['form'])){
            $listItem = $oStyleList->update($userId, $aPostParams );
            return $this->getResponse()->setContent(Json::encode($listItem));
            // exit();
        }
        $singleItem = $oStyleList->viewsingleitem($id);
        $__viewVariables['singleItem'] = $singleItem;

        $oStyleDefination = $this->getServiceLocator()->get('StyleDefinationTable');
        $styleItem = $oStyleDefination->liststyle($id);
        $__viewVariables['styleItem'] = $styleItem;
        return  $__viewVariables;
    }
    public function styledefinationAction() 
    {
        
        $finalArray = array();
        $aPostParams = $this->params()->fromPost();
        $aPost = explode("&",$aPostParams['form']); 
        foreach( $aPost as $val ){
          $tmp = explode( '=', $val );
          if (strpos($tmp[0], 'attr_value') !== false) {
                $finalArray[ $tmp[0]][] = $tmp[1];
            } else {
                $finalArray[ $tmp[0]] = $tmp[1];
            }
        }
        $oDefination = $this->getServiceLocator()->get('StyleDefinationTable');
        isset($finalArray['attr_name']) ? $attr = str_replace('+',' ',$finalArray['attr_name']) : $attr = false;
        isset($finalArray['attr_value']) ? $value = $finalArray['attr_value'] : $value = false;
        isset($finalArray['id-attr']) ? $id = $finalArray['id-attr'] : $id = false;
        $validator = new RecordExists(
            array(
                'table'   => 'StyleDefination',
                'field'   => 'styleId',
                'adapter' => $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'),
                'exclude' => ' attribute = "'.$attr.'"'
            )
        );
        $validator->isValid($id) ? $recordExists = 1 : $recordExists = 0;
       
        if($recordExists == 0){
             if($attr == false || $value == false){
                $attribute = 'Not Empty';
            }else{
                //var_dump($value); die;
                $attribute = $oDefination->insert($attr, $value, $id);

            }
        }else{
            $attribute = 'Record Exist';
        }

        return $this->getResponse()->setContent(Json::encode($attribute));
    }
    public function deletestyledefinationAction() 
    {
        $finalArray = array();
        $aPostParams = $this->params()->fromPost();
        $aPost = explode("&",$aPostParams['form']); 
        foreach( $aPost as $val ){
          $tmp = explode( '=', $val );
          if (strpos($tmp[0], 'attr_value') !== false) {
                $finalArray[ $tmp[0]][] = $tmp[1];
            } else {
                $finalArray[ $tmp[0]] = $tmp[1];
            }
        }
        $id = $finalArray['id'];
        $oDefination = $this->getServiceLocator()->get('StyleDefinationTable');
        $attribute = $oDefination->delete($id);
        return $this->getResponse()->setContent(Json::encode($attribute));
    }
    public function updatestyledefinationAction() 
    {
        $finalArray = array();
        $aPostParams = $this->params()->fromPost();
        $aPost = explode("&",$aPostParams['form']); 
        foreach( $aPost as $val ){
          $tmp = explode( '=', $val );
          if (strpos($tmp[0], 'attr_value') !== false) {
                $finalArray[ $tmp[0]][] = $tmp[1];
            } else {
                $finalArray[ $tmp[0]] = $tmp[1];
            }
        }

        //var_dump($finalArray); die;
        $number = $finalArray['number'];
        
        $oDefination = $this->getServiceLocator()->get('StyleDefinationTable');
        isset($finalArray['attr_name-'.$number]) ? $attr = $finalArray['attr_name-'.$number] : $attr = false;
        isset($finalArray['attr_value-'.$number]) ? $value = $finalArray['attr_value-'.$number] : $value = false;
        isset($finalArray['id']) ? $id = $finalArray['id'] : $id = false;
        

            if($attr == false || $value == false){
                $attribute = 'Not Empty';
            }else{
                $attribute = $oDefination->update($attr, $value, $number, $id );
            }
        return $this->getResponse()->setContent(Json::encode($attribute));
    }
    public function deletestyleAction() 
    { 
        $oAuth = $this->getServiceLocator()->get('AuthService');
        $userInfo = $oAuth->getIdentity();
        $this->userId = $userInfo->userId;
        $userId = $userInfo->userId;
        $userName = $userInfo->firstName;
        $this->layout()->firstName = $userInfo->firstName;
        $oStyleList = $this->getServiceLocator()->get('StyleListTable');
        $oVenueStyleList = $this->getServiceLocator()->get('VenuestyleTable');
        $listItem = $oStyleList->viewlist($userId);

        $aPostParams = $this->params()->fromPost();
        $value = explode("&",$aPostParams['form']);
        $arr_style = [];
        foreach($value as $val){
            $tmp = explode( '=', $val );
            $arr_style[ $tmp[0] ] = $tmp[1];
        }
        if (isset($arr_style["update-style"])) {
            $style = $oStyleList->delete($arr_style["update-style"]);
            $DelVenuest=$oVenueStyleList->deleterbystyleid($arr_style["update-style"]);
        }
        return $this->getResponse()->setContent(Json::encode($style));
    }
}