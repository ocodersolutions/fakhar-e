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

class ServiceController extends AbstractActionController {

    public function indexAction() {
        $oHelper = $this->getServiceLocator()->get('viewhelpermanager');
        //$oHelper->get('HeadScript')->appendFile('/vendor/aciTree/js/jquery.aciPlugin.min.js');
        //$oHelper->get('HeadScript')->appendFile('/vendor/aciTree/js/jquery.aciTree.dom.js');
        //$oHelper->get('HeadScript')->appendFile('/vendor/aciTree/js/jquery.aciTree.core.js');
        //$oHelper->get('HeadScript')->appendFile('/vendor/aciTree/js/jquery.aciTree.selectable.js');
        //$oHelper->get('HeadScript')->appendFile('/vendor/aciTree/js/jquery.aciTree.checkbox.js');
        //$oHelper->get('HeadScript')->appendFile('/vendor/aciTree/js/jquery.aciTree.radio.js');
        //$oHelper->get('HeadScript')->appendFile('/js/ui/jquery.ui.core.js');
        //$oHelper->get('HeadScript')->appendFile('/js/ui/jquery.ui.widget.js');
        //$oHelper->get('HeadScript')->appendFile('/js/ui/jquery.ui.mouse.js');
        //$oHelper->get('HeadScript')->appendFile('/js/ui/jquery.ui.draggable.js');
        //$oHelper->get('HeadScript')->appendFile('/js/ui/jquery.ui.droppable.js');
        //$oHelper->get('headLink')->appendStylesheet('/vendor/aciTree/css/aciTree.css');

        $oHelper->get('HeadScript')->appendFile('/js/ion.rangeSlider.js');
        $oHelper->get('headLink')->appendStylesheet('/css/normalizes.css');
        $oHelper->get('headLink')->appendStylesheet('/css/ion.rangeSlider.css');
        $oHelper->get('headLink')->appendStylesheet('/css/ion.rangeSlider.skinFlat.css');
        $oHelper->get('headLink')->appendStylesheet('/css/ion.rangeSlider.skinHTML5.css');

        $this->layout()->showSmallHeader = true;

        $this->layout()->showHeaderLinks = "NOT_LOGGED_IN";
        $oAuth = $this->getServiceLocator()->get('AuthService');
        if( $oAuth->hasIdentity() ) {
            $this->layout()->showHeaderLinks = "LOGGED_IN";
        }

        $__viewVariables = array();

        $oService = $this->getServiceLocator();
        $oFeedData = $oService->get('FeedDataTable');

        $oFeedMapping = $oService->get('FeedsMappingTable');
        $oAuth = $oService->get('AuthService');

        $aPostParams = $this->params()->fromPost();
        if (isset($aPostParams['ArticlesPerPage'])) {
            $oAuth->getStorage()->set('service', array('ArticlesPerPage' => $aPostParams['ArticlesPerPage']));
        }

        $aGetParams = $this->params()->fromQuery();
        $aServiceData = $oAuth->getStorage()->get('service');
        $__viewVariables['articlesPerPage'] = (isset($aServiceData['ArticlesPerPage']) ? $aServiceData['ArticlesPerPage'] : 8);

        $paginator = $oFeedData->getFeedData($aPostParams, true);
        $paginator->setCurrentPageNumber((isset($aGetParams['page']) ? $aGetParams['page'] : 1));
        $paginator->setItemCountPerPage($__viewVariables['articlesPerPage']);

        //print_r($paginator->getPages());

        $oAttributes = $oService->get('ProductAttributesTable');
        $__viewVariables['attributes'] = $oAttributes->getAttributesTree(1);

        $__viewVariables['feedData'] = $paginator;
        $__viewVariables['pages'] = $paginator->getPages();
        $__viewVariables['excludeCats'] = (isset($aGetParams['ec']) ? $aGetParams['ec'] : '');
        $aParams = array();
        $aParams['mappingType'] = 'brand';
        $allBrands = $oFeedMapping->getFeedMapping($aParams);

        $aParams = array();
        $aParams['mappingType'] = 'brand';
        $aParams['top'] = 1;
        $allTopBrands = $oFeedMapping->getFeedMapping($aParams);

        $aParams = array();
        $aParams['mappingType'] = 'store';
        $allStores = $oFeedMapping->getFeedMapping($aParams);

        $aParams = array();
        $aParams['mappingType'] = 'store';
        $aParams['top'] = 1;
        $allTopStores = $oFeedMapping->getFeedMapping($aParams);

        $__viewVariables['allBrands'] = $allBrands;
        $__viewVariables['allTopBrands'] = $allTopBrands;
        $__viewVariables['allStores'] = $allStores;
        $__viewVariables['allTopStores'] = $allTopStores;
        $jsonBrandArr = array();
        if (!empty($allBrands)) {
            foreach ($allBrands as $brand) {
                $jsonBrandArr[] = array('id' => $brand['id'], 'value' => $brand['title']);
            }
        }
        $jsonBrands = json_encode($jsonBrandArr);
        $__viewVariables['jsonBrands'] = $jsonBrands;
        $jsonStoreArr = array();
        if (!empty($allStores)) {
            foreach ($allStores as $store) {
                $jsonStoreArr[] = array('id' => $store['id'], 'value' => $store['title']);
            }
        }
        $jsonBrands = json_encode($jsonStoreArr);

        $__viewVariables['jsonStores'] = $jsonBrands;

        $allColors = $oFeedMapping->getDistinctColor();

        $maxRangePrice = $oFeedData->getMaxRangePrice();
        $__viewVariables['maxRangePrice'] = $maxRangePrice;
        $__viewVariables['allColors'] = $allColors;
        $oFeedMapping = $oService->get('OutfitsFeedsMappingTable');
        if(isset($aGetParams['outfit']) && $aGetParams['outfit']) {
            $outfitDetails = $oFeedMapping->getOutfitsMapping(array('outfit_id'=>$aGetParams['outfit']), true);
            $__viewVariables['outfits'] = $outfitDetails;
            $__viewVariables['designer'] = true;
        } else {
            $oProductsTable = $oService->get('OutfitsProductsTable');
            $outfitProductsTable = $oProductsTable->getOutfitsProducts(array(), null, true);            
            if($outfitProductsTable) {
                $outfitDetails = $oFeedMapping->getOutfitsMapping(array('outfit_id'=>$outfitProductsTable[0]['outfit_id']), true);
                $__viewVariables['outfits'] = $outfitDetails;
            }
            
        }
        $request = $this->getRequest();
        if ($request->isXmlHttpRequest()) {
            $viewModel = new ViewModel();
            $viewModel->setTerminal(true);
        }
        return $__viewVariables;
    }

    public function checkoutAction() {
        $this->layout()->showSmallHeader = true;
        $this->layout()->showHeaderLinks = "LOGGED_IN";
        $request = $this->params();
        $outfitID = $request->fromRoute('param1');
        $oService = $this->getServiceLocator();
        $oFeedMapping = $oService->get('OutfitsFeedsMappingTable');
        $__viewVariables = array();
        if(isset($outfitID) && $outfitID) {
            $outfitDetails = $oFeedMapping->getOutfitsMapping(array('outfit_id'=>$outfitID), true);
            $__viewVariables['outfits'] = $outfitDetails;
            $__viewVariables['designer'] = true;
        }
        
        return new ViewModel($__viewVariables);
    }

    //Function is used to get products on filter basis
    public function getfeedsAction() {
        $request = $this->getRequest();

        if ($request->isXmlHttpRequest()) {
            $oService = $this->getServiceLocator();
            $oFeedData = $oService->get('FeedDataTable');

            $aPostParams = $this->params()->fromPost();
            $aPostParams['catids'] = rtrim($aPostParams['catids'], ',');
            $aPostParams['brands'] = rtrim($aPostParams['brands'], ',');
            $aPostParams['colors'] = rtrim($aPostParams['colors'], ',');
            $aPostParams['stores'] = rtrim($aPostParams['stores'], ',');
            $aPostParams['deals'] = rtrim($aPostParams['deals'], ',');
            if (!empty($aPostParams['page'])) {
                $limit = $aPostParams['limit'];
                $offset = $aPostParams['limit'] * ($aPostParams['page'] - 1);
                $aPostParams['offset'] = $offset;
            }
            $paginator = $oFeedData->getFilterFeedData($aPostParams, true);

            $oAttributes = $oService->get('ProductAttributesTable');
            $__viewVariables['attributes'] = $oAttributes->getAttributesTree(1);

            $__viewVariables['feedData'] = $paginator;

            $viewModel = new ViewModel($__viewVariables);
            $viewModel->setTerminal(true);
            return $viewModel;
            // return $this->response;
        }
    }

    //Function is used to show detail of feed data

    public function feeddatadetailAction() {

        $request = $this->getRequest();

        if ($request->isXmlHttpRequest()) {

            $oService = $this->getServiceLocator();
            $oFeedData = $oService->get('FeedDataTable');
            $oFeedMapping = $oService->get('FeedsMappingTable');
            $feedDataId = $this->params()->fromRoute('feeddataid');
            $feedDataDetail = $oFeedData->getItem($feedDataId);
            $aParams = array();
            $aParams['mappingType'] = 'store';
            $aParams['feedId'] = $feedDataDetail['feedId'];
            $storeDetail = $oFeedMapping->getFeedMapping($aParams);
            $__viewVariables = array();
            $__viewVariables['feedDataDetail'] = $feedDataDetail;
            $__viewVariables['feedMappingDetail'] = $storeDetail;

            $oAttributes = $oService->get('ProductAttributesTable');
            $aParams = array();
            $aParams['type'] = 'size';
            $aParams['productUID'] = $feedDataDetail['uid'];
            $__viewVariables['feedDataSizes'] = $oAttributes->getFeedDataSizes($aParams);
            $viewModel = new ViewModel($__viewVariables);
            $viewModel->setTerminal(true);
            return $viewModel;
            // return $this->response;
        }
    }
    
    public function outfitsAction() {
        $oHelper = $this->getServiceLocator()->get('viewhelpermanager');
        $oHelper->get('HeadScript')->appendFile('/fancybox/source/jquery.fancybox.js');
        $oHelper->get('headLink')->appendStylesheet('/fancybox/source/jquery.fancybox.css');
        $oHelper->get('HeadScript')->appendFile('/js/jquery.datetimepicker.js');
        $oHelper->get('headLink')->appendStylesheet('/css/jquery.datetimepicker.css');
        $oAuth = $this->getServiceLocator()->get('AuthService');
        $oService = $this->getServiceLocator();
        $outfitTable = $oService->get('OutfitsProductsTable');
        $request = $this->getRequest();
        $response = new \stdClass();
        if(!$oAuth->getIdentity()) {
             return $this->redirect()->toUrl('/service');
        }
        if($request->isPost()) {
            $posts = $request->getPost();
            $searchResult = $outfitTable->getOutfitsProducts(array('user_id'=>$oAuth->getIdentity()->userId), $posts['searchkey']);
            $response->total = count($searchResult);
            $response->result = $searchResult;
        }
        
        if ($request->isXmlHttpRequest()) {
            die(\Zend\Json\Json::encode($response, true));
        }
        
        return new ViewModel(array('outfits'=>$outfitTable->getOutfitsProducts(array('user_id'=>$oAuth->getIdentity()->userId))));
        
    }
        
    public function addOutfitAction()
    {
        $request = $this->getRequest();
        $oService = $this->getServiceLocator();
        $this->oSession = $oService->get('StorageService')->getSession();
        $outfitTable = $oService->get('OutfitsProductsTable');
        
        $id = $request->getQuery('id', 0);
        
        if($request->isPost()) {
            $posts = $request->getPost();
            $posts['user_id'] = '4';
            $saveExist = $posts['save_exit'];
            unset($posts['save_exit']);
            $id = $outfitTable->save($posts->toArray());
            if($saveExist == 'yes') {
                return $this->redirect()->toUrl('/service/outfits');
            } else {
                return $this->redirect()->toUrl('/service?outfit='.$id);
            }
        }
        
        $viewModel = new ViewModel(
            array('outfit'=> current($outfitTable->getOutfitsProducts(array('outfit_id'=>$id))))
        );
        if ($request->isXmlHttpRequest()) {
            $viewModel->setTerminal(true);
        }
        return $viewModel;
    }
    
    public function updateOutfitAction()
    {
        $request = $this->getRequest();
        $oService = $this->getServiceLocator();
        $outfitMappingTable = $oService->get('OutfitsFeedsMappingTable');
        if($request->isPost()) {
            $posts = $request->getPost()->toArray();
            foreach($posts['feedid'] as $index=>$uid) {
                $data = array();
                $mapping = $outfitMappingTable->getOutfitsMapping(array('outfit_id'=>$posts['outfit_id'], 'feed_position'=> $index));
                //print_r($mapping); exit;
                if(isset($mapping[0]['id'])) {
                    $data['id'] = $mapping[0]['id'];
                } 
                $data['outfit_id'] = $posts['outfit_id'];
                $data['feeds_uid'] = $uid;
                $data['feed_position'] = $index;
                //print_r($data); exit;
                $mappingid[] = $outfitMappingTable->save($data);
            }
            
            //($posts); exit;
        }
        return $this->redirect()->toUrl('/service/outfits');
    }
    
    public function removeOutfitAction() {
        $request = $this->getRequest();
        $oService = $this->getServiceLocator();
        $outfitTable = $oService->get('OutfitsProductsTable');
        
        $id = $request->getQuery('id', 0);
        $outfitTable->remove($id);
        return $this->redirect()->toUrl('/service/outfits');
    }
    
}

