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
            $listItem = $oStyleList->viewlist($userId);
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
        $id= $this->params('id');
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
        if(isset($aPostParams['submit'])){
            $listItem = $oStyleList->update($userId, $id, $aPostParams );
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
        
        $aPostParams = $this->params()->fromPost();
        $oDefination = $this->getServiceLocator()->get('StyleDefinationTable');
        isset($aPostParams['attr_name']) ? $attr = $aPostParams['attr_name'] : $attr = false;
        isset($aPostParams['attr_value']) ? $value = $aPostParams['attr_value'] : $value = false;
        isset($aPostParams['id-attr']) ? $id = $aPostParams['id-attr'] : $id = false;
        $validator = new RecordExists(
            array(
                'table'   => 'StyleDefination',
                'field'   => 'styleId',
                'adapter' => $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'),
                'exclude' => ' attribute = "'.$attr.'" AND value = "'.$value.'"'
            )
        );
        $validator->isValid($id) ? $recordExists = 1 : $recordExists = 0;
        if($recordExists == 0){
             if($attr == false || $value == false){
                $attribute = 'Not Empty';
            }else{
                $attribute = $oDefination->insert($attr, $value, $id);
            }
        }else{
            $attribute = 'Record Exist';
        }

        return $this->getResponse()->setContent(Json::encode($attribute));
    }
    public function updatestyledefinationAction() 
    {
        $aPostParams = $this->params()->fromPost();
        $number = $aPostParams['number'];
        $oDefination = $this->getServiceLocator()->get('StyleDefinationTable');
        isset($aPostParams['attr_name-'.$number]) ? $attr = $aPostParams['attr_name-'.$number] : $attr = false;
        isset($aPostParams['attr_value-'.$number]) ? $value = $aPostParams['attr_value-'.$number] : $value = false;
        isset($aPostParams['id']) ? $id = $aPostParams['id'] : $id = false;
            if($attr == false || $value == false){
                $attribute = 'Not Empty';
            }else{
                $attribute = $oDefination->update($attr, $value, $number, $id );
            }
        return $this->getResponse()->setContent(Json::encode($attribute));
    }
    public function deletestyleAction() 
    {
        // exit();
        // $listItem = $this->listItem;
        $oAuth = $this->getServiceLocator()->get('AuthService');
        $userInfo = $oAuth->getIdentity();
        $this->userId = $userInfo->userId;
        $userId = $userInfo->userId;
        $userName = $userInfo->firstName;
        $this->layout()->firstName = $userInfo->firstName;
        $oStyleList = $this->getServiceLocator()->get('StyleListTable');
        $listItem = $oStyleList->viewlist($userId);

        $aPostParams = $this->params()->fromPost();

        $result_data = "";
        if (count($aPostParams)) {
            $oStyleList->delete($aPostParams["del_style"]);  
        }
        return 0;
    }
}