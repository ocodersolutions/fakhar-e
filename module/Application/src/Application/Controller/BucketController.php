<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Model\FeedDataTable;
use Zend\Session\Container;
use Zend\Console\Request as ConsoleRequest;
use Ocoder\Base\BaseActionController;

class BucketController extends BaseActionController
{
	protected $_userid;
	protected $_oService;
    public function __construct(){
    }

	public function indexAction() 
	{
        $aGetParams = $this->params()->fromQuery();
        $tabactive = isset($aGetParams['tabactive']) ? $aGetParams['tabactive'] : '';
      

		$this->layout()->showSmallHeader = true;
        $this->layout()->hideFooter = true;

        $this->layout()->showHeaderLinks = "NOT_LOGGED_IN";
        $oAuth = $this->getServiceLocator()->get('AuthService');
        if ($oAuth->hasIdentity() && $oAuth->getIdentity()->userType==2) {
            $this->layout()->showHeaderLinks = "LOGGED_IN";
            $userInfo = $oAuth->getIdentity();
            $this->userId = $userInfo->userId;
            $this->layout()->firstName = $userInfo->firstName;
        } else {
            $this->redirect()->toRoute('auth');
        }

        $__viewVariables['tabactive']= -1;
		$__viewVariables = array();
          if($tabactive!='' ||  $tabactive!=null) {
            $__viewVariables['tabactive']= $tabactive;
        }
		$oUserProfile = $this->getServiceLocator()->get('UserProfilesTable');
		$aStyles = $oUserProfile->getStyleQuestions();
		//unset($aStyles['never_wear']);
		$styleArr = array();
		foreach ($aStyles as $key => $value) {
			foreach ($value as $value) {
				$pieces = preg_split('/(?=[A-Z])/', $value['name']);
				$val = implode(' ', array_filter($pieces));
				$styleArr[ucwords(str_replace('_', ' ', $key)).': '.$val] = array('img'=>$value['img'],'value' =>$value['value'],'BucketID'=>$key.'__'.$value['BucketID']);
			}
		} 
		$__viewVariables['aStyles'] = $styleArr;


		//get all definition
		$bucketdefinitionTable = $this->getServiceLocator()->get('BucketDefinitionTable');
		$listDefinition = $bucketdefinitionTable ->getDefinition();
		$__viewVariables['listDefinition'] = $listDefinition;

        $listBucketID = array(); 
        foreach ($__viewVariables['aStyles'] as $key => $value) {
            $listBucketID[] = $value['BucketID'];
        }       
         $__viewVariables['listBucketID'] = $listBucketID;
		return 	$__viewVariables;
	}

	public function addAction(){
		$this->layout()->showHeaderLinks = "NOT_LOGGED_IN";
        $oAuth = $this->getServiceLocator()->get('AuthService');
        if ($oAuth->hasIdentity()) {
            $this->layout()->showHeaderLinks = "LOGGED_IN";
            $userInfo = $oAuth->getIdentity();
            $userid = $userInfo->userId;
            $this->layout()->firstName = $userInfo->firstName;
        } else {
            $this->redirect()->toRoute('auth');
        }
        $tabactive='';
		if($this->request->isPost()){
            $tabactive = $this->request->getPost()['tabActive'];
  
			$params = array(
				'title' 	=> $this->request->getPost()['title'],
				'bucketID'  => $this->request->getPost()['bucketID'],
				'userid' 	=> $userid,
			);
			$bucketdefinitionTable = $this->getServiceLocator()->get('BucketDefinitionTable');
			$id = $bucketdefinitionTable->addDefinition($params);
			if($id) {
				 $this->flashMessenger()->addSuccessMessage('Add new definition successully!');
			} else {
				  $this->flashMessenger()->addErrorMessage('Have Error!');
			}

		}

		 return $this->redirect()->toUrl('/bucket/edit-bucket-attribute?bdid='.$id.'&tabactive='.$tabactive);
	}
	public function deleteAction(){

		$id = $this->params('id');
		if($id > 0) {
			$bucketdefinitionTable = $this->getServiceLocator()->get('BucketDefinitionTable');
			$bucketdefinitionTable->deleteDefinition(array('id'=>$id));
			$this->flashMessenger()->addSuccessMessage('Delete Success!');
		}
		$this->redirect()->toRoute('bucket');
	}

	public function editAction(){
		if($this->request->isPost()){
			$id = $this->request->getPost()['definitionID'];
			if($id > 0) {
				$bucketdefinitionTable = $this->getServiceLocator()->get('BucketDefinitionTable');
				$id = $bucketdefinitionTable->editDefinition($id,array('title'=> $this->request->getPost()['title']));
				$this->flashMessenger()->addSuccessMessage('Edit Success!');
			}
		}
		$this->redirect()->toRoute('bucket');
	}

	public function editBucketAttributeAction(){
        

		$this->layout()->hideFooter = true;
        $oAuth = $this->getServiceLocator()->get('AuthService');
        if ($oAuth->hasIdentity()) {
            $this->layout()->showHeaderLinks = "LOGGED_IN";
        }
        //$this->layout('layout/empty');
        $__viewVariables = array();
        $aGetParams = $this->params()->fromQuery();
        $tabactive = $aGetParams['tabactive'];
        $__viewVariables['tabactive'] = $tabactive;
        $__viewVariables['item']['id'] =   $aGetParams['bdid'];

        

        $aPostParams = $this->params()->fromPost();
     
        $oService = $this->getServiceLocator();
        //$viewModel->setTerminal(true); // Disable layout.
        $oAttributes = $oService->get('ProductAttributesTable');
        // $__viewVariables['attributes'] = $oAttributes->getAttributesTree(0, true, '3D');

        // $__viewVariables['level1CategoryIds'] = $oAttributes->getLevel1CategoryIds('3D');
        // $__viewVariables['level2CategoryIds'] = $oAttributes->getLevel2CategoryIds('3D');
        $__viewVariables['attributes'] = $oAttributes->getAttributesTree(0, true, '3D');

        $__viewVariables['level1CategoryIds'] = $oAttributes->getLevel1CategoryIds('3D');
        $__viewVariables['level2CategoryIds'] = $oAttributes->getLevel2CategoryIds('3D');
        $__viewVariables['categoryTypeIds'] = $oAttributes->getCategoryTypeIds();



        $oBucketAttrData = $oService->get('BucketAttributeTable');
        $__viewVariables['bucket_attributes'] = $oBucketAttrData->getAttributes($aGetParams['bdid'] );
     

      
        // $oProductArrtibutes = $oService->get('ProductAttributesTable');
        // $__viewVariables['product_attributes'] = array_filter($oProductArrtibutes->getAttributes($__viewVariables['item']['uid'], 'title'));    
        
        $__viewVariables['bucket_attributes_ids'] = $__viewVariables['product_attributes_ids'] = array();
        foreach($__viewVariables['bucket_attributes'] as $catLevel1 ) {
            foreach($catLevel1 as $catLevel2 ) {
                $__viewVariables['bucket_attributes_ids'][] = $catLevel2['attributeId'];
            }            
        }

        if($this->getRequest()->isPost()){

           $data = array();
           $bucketdefinitionID = $aGetParams['bdid'];

           if($bucketdefinitionID > 0) {
                $data = array();
                $aPostParams = $this->params()->fromPost();
                if(isset($aPostParams['categories']) &&!empty($aPostParams['categories'])){
                
                $oBucketAttrData->deleteAllBucketAttributes($bucketdefinitionID);
           
                
                $dataPost = json_decode($aPostParams['categories']);
                foreach ($dataPost as $value) {
                    $aParts = explode(':', $value);
                    if( $aParts[0] == 'catagory' || $aParts[0] == 'attribute' ) {
                        $attribParts = explode(',', $aParts[1]);
                        foreach($attribParts as $val) {
                            $aAtrParts = explode('#', $val);
                            $aRefAtrribs = $oBucketAttrData->addBucketAttributes_Categories($bucketdefinitionID, $aAtrParts[0], (isset($aAtrParts[1])?$aAtrParts[1]:'inclusive') );
                        }
                    }
                }
                
                               
            }
    
            return $this->redirect()->toUrl("/bucket?tabactive=".$tabactive); 
           }   
        }
        return $__viewVariables;
	}
}