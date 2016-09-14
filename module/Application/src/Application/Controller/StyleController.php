<?php
namespace Application\Controller;

use Zend\View\Model\ViewModel;
use Application\Model\FeedDataTable;
use Application\Model\StyleListTable;
use Application\Model\AttributeListTable;
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
        return  $__viewVariables;
    }
    public function deletestyleAction() 
    {
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
            foreach ($listItem as $item) {
                
                if($item["id"] == $aPostParams["del_style"])
                {
                    $oStyleList->delete($item["id"]); 
                    $listItem = $oStyleList->viewlist($userId);
                    
                    foreach($listItem as $item ) 
                    {
                        $result_data .= 
                         "<tr>
                         <td>" . $item["id"] . "</td>
                         <td>" . $item["title"] . "</td>
                         <td>" . $userName . "</td>
                         <td>" . $item["isActive"] . "</td>
                         <td> <button type='button' class='btn btn-info'><a href='style/defination/" . $item["id"] . "'>Edit</a></button> 
                                    <button type='button' class='btn btn-danger' id='btn_delete_style' data-toggle='modal' data-delete='" . $item["id"] . "' data-target='#myModal'>Delete</button> 
                             </td>
                         </tr>";
                    }
                    

                    break;
                }
            }
            // exit($result_data);
            echo $result_data;
        }
    }
    public function mystyleAction() {
        echo "12131";
        // // die("success");
        //  $id= $this->params('id');
        // var_dump($_REQUEST); 
        
        // $this->redirect()->toRoute('style/defination/2');
    }
}