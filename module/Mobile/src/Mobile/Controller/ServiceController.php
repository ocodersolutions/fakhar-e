<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Mobile\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Json\Json;
use Zend\View\Helper\ServerUrl;
use Zend\Db\Sql\Where;
use Ocoder\Base\BaseActionController;
use Zend\Session\Container;
class ServiceController extends BaseActionController {

    public function indexAction() {
        //$oHelper = $this->getServiceLocator()->get('viewhelpermanager');
        // $oHelper->get('HeadScript')->appendFile('/templates/mobile/aciTree/js/jquery.aciPlugin.min.js');
        // $oHelper->get('HeadScript')->appendFile('/vendor/aciTree/js/jquery.aciTree.dom.js');
        // $oHelper->get('HeadScript')->appendFile('/vendor/aciTree/js/jquery.aciTree.core.js');
        // $oHelper->get('HeadScript')->appendFile('/vendor/aciTree/js/jquery.aciTree.selectable.js');
        // $oHelper->get('HeadScript')->appendFile('/vendor/aciTree/js/jquery.aciTree.checkbox.js');
        // $oHelper->get('HeadScript')->appendFile('/vendor/aciTree/js/jquery.aciTree.radio.js');
        // $oHelper->get('HeadScript')->appendFile('/vendor/jquery-ui.min.js');
        //$oHelper->get('HeadScript')->appendFile('/js/ui/jquery.ui.core.js');
        //$oHelper->get('HeadScript')->appendFile('/js/ui/jquery.ui.widget.js');
        //$oHelper->get('HeadScript')->appendFile('/js/ui/jquery.ui.mouse.js');
        //$oHelper->get('HeadScript')->appendFile('/js/ui/jquery.ui.draggable.js');
        //$oHelper->get('HeadScript')->appendFile('/js/ui/jquery.ui.droppable.js');

        //$oHelper->get('headLink')->appendStylesheet('/vendor/aciTree/css/aciTree.css');

        $__viewVariables = array();
        $aGetParams = $this->params()->fromQuery();
        
        $this->layout()->showSmallHeader = true;
        $this->layout()->hideFooter = true;

        $this->layout()->showHeaderLinks = "NOT_LOGGED_IN";
        $oAuth = $this->getServiceLocator()->get('AuthService');

        $userId = null;
        if ($oAuth->hasIdentity()) {
            $this->layout()->showHeaderLinks = "LOGGED_IN";
            $userInfo = $oAuth->getIdentity();
            $userId = $userInfo->userId;
            $this->layout()->firstName = $userInfo->firstName;
        } else {
            if (isset($aGetParams['outfit']) && $aGetParams['outfit']) {

                $__viewVariables['fromSocialMedia'] = true;
                list($width, $height, $type, $attr) = getimagesize(PATH_APPLICATION."/public/feed/outfits/{$aGetParams['outfit']}.jpg"); 
                $this->layout()->additionalMeta = '
                    <meta property="og:image" content="http://'.URL_APPLICATION."/feed/outfits/{$aGetParams['outfit']}.jpg".'" />
                    <meta property="og:title" content="Lnove" />
                    <meta property="og:site_name" content="Lnove"/>
                    <meta property="og:url" content="http://'.URL_APPLICATION.'/service?outfit='.$aGetParams['outfit'].'" />
                    <meta property="og:description" content="Lnove" />
                    <meta property="og:image:width" content="'.$width.'" />
                    <meta property="og:image:height" content="'.$height.'" />
                ';
                $__viewVariables['shareOutfitImage'] =  'http://'.URL_APPLICATION."/feed/outfits/{$aGetParams['outfit']}.jpg";   
                return     $__viewVariables;           
            }
            else {
                $this->redirect()->toRoute('auth');
            }
        }
        $__viewVariables['userEmail'] = @$userInfo->email; 

        $oService = $this->getServiceLocator();
        $oFeedData = $oService->get('FeedDataTable');

        $oFeedMapping = $oService->get('FeedsMappingTable');
        $oAuth = $oService->get('AuthService');

        $oArticleCloseset = $oService->get('ArticleClosesetTable');
        $likedarticles = $oArticleCloseset->getLikedArticles($userId);
        $likearray = array();
        if(count($likedarticles)>0) {
           foreach($likedarticles as $likedarticle) {
                $likearray[] = $likedarticle['feeddataid'];
            }
        }
        $__viewVariables['likearray'] = $likearray;

        $aPostParams = $this->params()->fromPost();
        if (isset($aPostParams['ArticlesPerPage'])) {
            $oAuth->getStorage()->set('service', array('ArticlesPerPage' => $aPostParams['ArticlesPerPage']));
        }
     
        $aServiceData = $oAuth->getStorage()->get('service');

        $__viewVariables['articlesPerPage'] = (isset($aServiceData['ArticlesPerPage']) ? $aServiceData['ArticlesPerPage'] : PRODUCTS_PER_PAGE);

        $paginator = $oFeedData->getFeedData($aPostParams, true, $userId, null, true);
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
        $aParams['mappingType'] = 'store - remove stores';
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

        $__viewVariables['actualMaxRangePrice'] = $oFeedData->getMaxRangePrice();
        $__viewVariables['maxRangePrice'] = 1000;
        $__viewVariables['allColors'] = $allColors;
        $oFeedMapping = $oService->get('OutfitsFeedsMappingTable');
        $__viewVariables['defaultVenue'] = array();
        if (isset($aGetParams['outfit']) && $aGetParams['outfit']) {
            $outfitDetails = $oFeedMapping->getOutfitsMapping(array('outfit_id' => $aGetParams['outfit']), true);
            $__viewVariables['outfits'] = $outfitDetails;
            $__viewVariables['designer'] = true;
        } else {
            $oProductsTable = $oService->get('OutfitsProductsTable');
            $currentPage = 1; // set page default for router
            $row_per_page = 2;
            $rowStart = ($currentPage * $row_per_page) - $row_per_page;

            $params = array(
                'limit' => $row_per_page,
                'offset' => $rowStart,
                'where' => array('status'=>1),
            );
            $outfits = $oProductsTable->getOutfits($params);
            
            $outfit_details = array();
            foreach ($outfits as $value) {
                $outfit_details[$value['outfit_id']] = $oFeedMapping->getOutfitsMapping(array('outfit_id' => $value['outfit_id']), true);
            }
            $__viewVariables['outfit_details'] = $outfit_details;
         
            // $outfitProductsTable = $oProductsTable->getOutfitsProducts(array(), null, true);
            // if ($outfitProductsTable) {
            //     $outfitDetails = $oFeedMapping->getOutfitsMapping(array('outfit_id' => $outfitProductsTable[0]['outfit_id']), true);
            //     $__viewVariables['outfits'] = $outfitDetails;
            //     $__viewVariables['outfit_id']  = $outfitProductsTable[0]['outfit_id'];
            // }
        }
        $request = $this->getRequest();
        if ($request->isXmlHttpRequest()) {
            $viewModel = new ViewModel();
            $viewModel->setTerminal(true);
        }

        $__viewVariables['listItemCart'] =  $this->getItemCart();

        $this->layout()->__viewVariables = $__viewVariables;
        return $__viewVariables;
    }
    public function articlesAction() {
        $oHelper = $this->getServiceLocator()->get('viewhelpermanager');
        $oHelper->get('HeadScript')->appendFile('/vendor/aciTree/js/jquery.aciPlugin.min.js');
        $oHelper->get('HeadScript')->appendFile('/vendor/aciTree/js/jquery.aciTree.dom.js');
        $oHelper->get('HeadScript')->appendFile('/vendor/aciTree/js/jquery.aciTree.core.js');
        $oHelper->get('HeadScript')->appendFile('/vendor/aciTree/js/jquery.aciTree.selectable.js');
        $oHelper->get('HeadScript')->appendFile('/vendor/aciTree/js/jquery.aciTree.checkbox.js');
        $oHelper->get('HeadScript')->appendFile('/vendor/aciTree/js/jquery.aciTree.radio.js');
        $oHelper->get('HeadScript')->appendFile('/vendor/jquery-ui.min.js');
        //$oHelper->get('HeadScript')->appendFile('/js/ui/jquery.ui.core.js');
        //$oHelper->get('HeadScript')->appendFile('/js/ui/jquery.ui.widget.js');
        //$oHelper->get('HeadScript')->appendFile('/js/ui/jquery.ui.mouse.js');
        //$oHelper->get('HeadScript')->appendFile('/js/ui/jquery.ui.draggable.js');
        //$oHelper->get('HeadScript')->appendFile('/js/ui/jquery.ui.droppable.js');

        $oHelper->get('headLink')->appendStylesheet('/vendor/aciTree/css/aciTree.css');

        $__viewVariables = array();

        $this->layout()->showSmallHeader = true;

        $this->layout()->showHeaderLinks = "NOT_LOGGED_IN";
        $oAuth = $this->getServiceLocator()->get('AuthService');
        if ($oAuth->hasIdentity()) {
            $this->layout()->showHeaderLinks = "LOGGED_IN";
            $userInfo = $oAuth->getIdentity();
            $userId = $userInfo->userId;
            $this->layout()->firstName = $userInfo->firstName;
        } else {
            $this->redirect()->toRoute('auth');
        }

        $oService = $this->getServiceLocator();
        $oFeedData = $oService->get('FeedDataTable');

        $oFeedMapping = $oService->get('FeedsMappingTable');
        $oAuth = $oService->get('AuthService');

        $oArticleCloseset = $oService->get('ArticleClosesetTable');
        $likedarticles = $oArticleCloseset->getLikedArticles($userId);
        $likearray = array();
        if(count($likedarticles)>0) {
           foreach($likedarticles as $likedarticle) {
                $likearray[] = $likedarticle['feeddataid'];
            }
        }
        $__viewVariables['likearray'] = $likearray;

        $aPostParams = $this->params()->fromPost();
        if (isset($aPostParams['ArticlesPerPage'])) {
            $oAuth->getStorage()->set('service', array('ArticlesPerPage' => $aPostParams['ArticlesPerPage']));
        }

        $aGetParams = $this->params()->fromQuery();
        $aServiceData = $oAuth->getStorage()->get('service');
        $__viewVariables['articlesPerPage'] = (isset($aServiceData['ArticlesPerPage']) ? $aServiceData['ArticlesPerPage'] : 25);

        $paginator = $oFeedData->getFeedData($aPostParams, true, $userId, null, true);
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
        if (isset($aGetParams['outfit']) && $aGetParams['outfit']) {
            $outfitDetails = $oFeedMapping->getOutfitsMapping(array('outfit_id' => $aGetParams['outfit']), true);
            $__viewVariables['outfits'] = $outfitDetails;
            $__viewVariables['designer'] = true;
        } else {
            $oProductsTable = $oService->get('OutfitsProductsTable');
            $outfitProductsTable = $oProductsTable->getOutfitsProducts(array(), null, true);
            if ($outfitProductsTable) {
                $outfitDetails = $oFeedMapping->getOutfitsMapping(array('outfit_id' => $outfitProductsTable[0]['outfit_id']), true);
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
        if (isset($outfitID) && $outfitID) {
            $outfitDetails = $oFeedMapping->getOutfitsMapping(array('outfit_id' => $outfitID), true);
            $__viewVariables['outfits'] = $outfitDetails;
            $__viewVariables['designer'] = true;
        }

        return new ViewModel($__viewVariables);
    }

    /**
     * Function for add likes information of feed
     * 
     * @param int id this is feed id for which feed likes by user
     * 
     * @return array json result array with message
     */
    public function likefeedAction() {
        $status = array();
        $oAuth = $this->getServiceLocator()->get('AuthService');
        $userInfo = $oAuth->getIdentity();
        if ($oAuth->hasIdentity()) {
            $request = $this->getRequest();
            if ($request->isPost()) {
                $data = $request->getPost();
                $articleId = base64_decode($data['feedId']);
                $userId = $userInfo->userId;
                $articleData['articleId'] = $articleId;
                $articleData['userId'] = $userId;

                $oService = $this->getServiceLocator();
                $oArticleData = $oService->get('ArticleLikesTable');

                $articleLikes = $oArticleData->getArticleLikes($articleId, $userId);

                if (isset($articleLikes) && !empty($articleLikes)) {
                    $status['mgs'] = "You have already marked as likes";
                    $status['status'] = "N";
                } else {
                    $result = $oArticleData->saveArticleLiekes($articleData);
                    if ($result) {
                        $articleLikes = $oArticleData->getArticleLikesCount($articleId);
                        $status['status'] = "Y";
                        $status['count'] = $articleLikes;
                        $status['idf'] = $articleId;
                    }
                }
            } else {
                $status['mgs'] = "Some things wrong";
                $status['status'] = "N";
            }
        } else {
            $status['mgs'] = "Please login first";
            $status['status'] = "N";
        }
        return $this->getResponse()->setContent(Json::encode($status));
    }

    /**
     * Function for add likes information of feed
     * 
     * @param int id this is feed id for which feed likes by user
     * 
     * @return array json result array with message
     */
    public function outfitclossetAction() {
        $status = array();
        $oAuth = $this->getServiceLocator()->get('AuthService');
        $userInfo = $oAuth->getIdentity();
        if ($oAuth->hasIdentity()) {
            $request = $this->getRequest();
            if ($request->isPost()) {
                $data = $request->getPost();
                $articleId = base64_decode($data['closesetId']);
                $userId = $userInfo->userId;
                $outfitData['articleId'] = $articleId;
                $outfitData['userId'] = $userId;
                $oService = $this->getServiceLocator();
                $oOutfitData = $oService->get('OutfitClosesetTable');
                $oArticleData = $oService->get('ArticleClosesetTable');
                $oFeedMapping = $oService->get('OutfitsFeedsMappingTable');
                $outfitLikes = $oOutfitData->getArticleLikes($articleId, $userId);
                if ($outfitLikes != false) {
                    $status['mgs'] = "You have already added this outfit to your closet";
                    $status['status'] = "N";
                } else {
                    $outfitDetails = $oFeedMapping->getOutfitsMapping(array('outfit_id' => $articleId), true);
                    foreach ($outfitDetails as $outfitDetailsA) {
                        $Id = $outfitDetailsA['id'];
                        if (isset($Id) && !empty($Id)) {
                            $articleLikes = $oArticleData->getArticleLikes($Id, $userId);
                            if (isset($articleLikes) && !empty($articleLikes)) {
                                
                            } else {
                                $articleData['articleId'] = $Id;
                                $articleData['userId'] = $userId;
                                $result = $oArticleData->saveArticleLiekes($articleData);
                            }
                        }
                    }
                    $result = $oOutfitData->saveArticleLiekes($outfitData);
                    if ($result) {
                        $articleLikes = $oOutfitData->getArticleLikesCount($articleId);
                        $status['status'] = "Y";
                        $status['count'] = $articleLikes;
                        $status['idf'] = $articleId;
                        $status['mgs'] = 'Oufit has been successfully added to your closet';
                        
                    }
                }
            } else {
                $status['mgs'] = "Some things wrong";
                $status['status'] = "N";
            }
        } else {
            $status['mgs'] = "Please login first";
            $status['status'] = "N";
        }
        return $this->getResponse()->setContent(Json::encode($status));
    }

    /**
     * Function for add likes information of feed
     * 
     * @param int id this is feed id for which feed likes by user
     * 
     * @return array json result array with message
     */
    public function likeclossetAction() {
        $status = array();
        $oAuth = $this->getServiceLocator()->get('AuthService');
        $userInfo = $oAuth->getIdentity();
        if ($oAuth->hasIdentity()) {
            $request = $this->getRequest();
            if ($request->isPost()) {
                $data = $request->getPost();
                $articleId = base64_decode($data['closesetId']);
                $userId = $userInfo->userId;
                $articleData['articleId'] = $articleId;
                $articleData['userId'] = $userId;
                $oService = $this->getServiceLocator();
                $oArticleData = $oService->get('ArticleClosesetTable');

                $articleLikes = $oArticleData->getArticleLikes($articleId, $userId);

                if (isset($articleLikes) && !empty($articleLikes)) {
                    $status['mgs'] = "You have already added this article to your closet";
                    $status['status'] = "N";
                } else {
                    $result = $oArticleData->saveArticleLiekes($articleData);
                    if ($result) {
                        $articleLikes = $oArticleData->getArticleLikesCount($articleId);
                        $status['status'] = "Y";
                        $status['count'] = $articleLikes;
                        $status['idf'] = $articleId;
                        $status['mgs'] = "Article was successfully added to your closet";
                    }
                }
            } else {
                $status['mgs'] = "Some things wrong";
                $status['status'] = "N";
            }
        } else {
            $status['mgs'] = "Please login first";
            $status['status'] = "N";
        }
        return $this->getResponse()->setContent(Json::encode($status));
    }

    /**
     * Function for add likes information of feed
     * 
     * @param int id this is feed id for which feed likes by user
     * 
     * @return array json result array with message
     */
    public function articleboughtAction() {
        $this->layout()->showHeaderLinks = "NOT_LOGGED_IN";
        $oAuth = $this->getServiceLocator()->get('AuthService');
        if ($oAuth->hasIdentity()) {
            $this->layout()->showHeaderLinks = "LOGGED_IN";
        }
        $id = $this->params()->fromRoute('id');
        $feedId = base64_decode($id);
        $oService = $this->getServiceLocator();
        $oFeedData = $oService->get('FeedDataTable');
        $feedDataDetail = $oFeedData->getItem($feedId);
        $status = array();
        $oAuth = $this->getServiceLocator()->get('AuthService');
        $userInfo = $oAuth->getIdentity();
        if ($oAuth->hasIdentity()) {
            $articleId = $feedId;
            $userId = $userInfo->userId;
            $articleData['articleId'] = $articleId;
            $articleData['userId'] = $userId;
            $oService = $this->getServiceLocator();
            $oArticleData = $oService->get('ArticleBoughtTable');
            $articleLikes = $oArticleData->getArticleLikes($articleId, $userId);
            if (isset($articleLikes) && !empty($articleLikes)) {
                
            } else {
                $result = $oArticleData->saveArticleLiekes($articleData);
            }
        }
        $__viewVariables['feedDataDetail'] = $feedDataDetail;
        $viewModel = new ViewModel($__viewVariables);
        return $__viewVariables;
        $viewModel->setTerminal(true);
        //return $viewModel;
    }

    //Function is used to get products on filter basis
    public function getfeedsAction() {
        $request = $this->getRequest();
        $oAuth = $this->getServiceLocator()->get('AuthService');
        $userInfo = $oAuth->getIdentity();
        $userId = $userInfo->userId;
        $aQueryParams = $this->params()->fromQuery();
         
        if ($request->isXmlHttpRequest() || (isset($aQueryParams['test']) && $aQueryParams['test']=='yes') ) { 
            $oService = $this->getServiceLocator();
            $oFeedData = $oService->get('FeedDataTable');

            $oArticleCloseset = $oService->get('ArticleClosesetTable');
            $likedarticles = $oArticleCloseset->getLikedArticles($userId);
            $likearray = array();
            if(count($likedarticles)>0) {
                foreach($likedarticles as $likedarticle) {
                    $likearray[] = $likedarticle['feeddataid'];
                }
            }
            $__viewVariables['likearray'] = $likearray;

            $aPostParams = $this->params()->fromPost();
            if(isset($aQueryParams['test']) && $aQueryParams['test']=='yes') {
                $aPostParams = $this->params()->fromQuery();
            }
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
            $paginator = $oFeedData->getFilterFeedData($aPostParams, true,$userId,null,'service');

            $oAttributes = $oService->get('ProductAttributesTable');
            $__viewVariables['attributes'] = $oAttributes->getAttributesTree(1);

            $__viewVariables['feedData'] = $paginator;

            $__viewVariables['listItemCart'] =  $this->getItemCart();

            $viewModel = new ViewModel($__viewVariables);
            $viewModel->setTerminal(true);
            return $viewModel;
            // return $this->response;
        }
    }
    public function getoutfitsAction() {
        $request = $this->getRequest();
        if ($request->isXmlHttpRequest()) { 
            $aPostParams = $this->params()->fromPost();
            $oService = $this->getServiceLocator();
            $oProductsTable = $oService->get('OutfitsProductsTable');
            $oFeedMapping = $oService->get('OutfitsFeedsMappingTable');
            $currentPage = $aPostParams['outfit_page']; // set page default for router
            $row_per_page = 2;
            $rowStart = ($currentPage * $row_per_page) - $row_per_page;

            // Get all root Carreer
            $params = array(
                'limit' => $row_per_page,
                'offset' => $rowStart,
                'where' => array('status'=>1),
            );
            $outfits = $oProductsTable->getOutfits($params);
         
            $outfit_details = array();
            foreach ($outfits as $value) {
                $outfit_details[$value['outfit_id']] = $oFeedMapping->getOutfitsMapping(array('outfit_id' => $value['outfit_id']), true);
            }
            $__viewVariables['outfit_details'] = $outfit_details;

            $viewModel = new ViewModel($__viewVariables);
            $viewModel->setTerminal(true);
            return $viewModel;
            // return $this->response;
        }
    }

    //Function is used to show detail of feed data

    public function feeddatadetailAction() {
        //$pre_url = $this->getRequest()->getHeader('Referer')->getUri();
        
        $request = $this->getRequest();
        $articleDetailData = array();
        if ($request->isXmlHttpRequest()) {
            $userId = 0;
            $this->layout()->showHeaderLinks = "NOT_LOGGED_IN";
            $oAuth = $this->getServiceLocator()->get('AuthService');
            if ($oAuth->hasIdentity()) {
                $userInfo = $oAuth->getIdentity();
                $userId = $userInfo->userId;
                $this->layout()->showHeaderLinks = "LOGGED_IN";
            }

            $oService = $this->getServiceLocator();
            $oFeedData = $oService->get('FeedDataTable');
            $oFeedMapping = $oService->get('FeedsMappingTable');
            //$feedDataId = $this->params()->fromRoute('feeddataid');
            //$feedDataId = $this->params('id');
            $feedDataId = base64_decode($request->getPost()['id']);
            $feedDataDetail = $oFeedData->getItem($feedDataId);


            /*         * ***********block for inserting data into table when user view this article********** */
            $oArticleDetail = $oService->get('ArticleDetailTable');
            $articleId = $feedDataDetail['id'];
            $articleDetailData['feeddataid'] = $articleId;
            $articleDetailData['userId'] = $userId;
            $result = $oArticleDetail->getArticleDetailInfo($articleId, $userId);
            if (isset($result) && empty($result)) {
                $oArticleDetail->saveArticleDetailInfo($articleDetailData);
            }
            /**         * **********block for inserting data into table when user view this article********** */
            $aParams = array();
            $aParams['mappingType'] = 'store';
            $aParams['feedId'] = $feedDataDetail['feedId'];
            $storeDetail = $oFeedMapping->getFeedMapping($aParams);
            $__viewVariables = array();

            /*My code*/
                    $oArticleCloseset = $oService->get('ArticleClosesetTable');
            $likedarticles = $oArticleCloseset->getLikedArticles($userId);
            $likearray = array();
            if(count($likedarticles)>0) {
               foreach($likedarticles as $likedarticle) {
                    $likearray[] = $likedarticle['feeddataid'];
                }
            }
             $__viewVariables['likearray'] = $likearray;
            /*end My code*/
            $__viewVariables['feedDataDetail'] = $feedDataDetail;
            $__viewVariables['feedMappingDetail'] = $storeDetail;
            $oAttributes = $oService->get('ProductAttributesTable');
            $aParams = array();
            $aParams['type'] = 'size';
            $aParams['productUID'] = $feedDataDetail['uid'];
            $__viewVariables['feedDataSizes'] = $oAttributes->getFeedDataSizes($aParams);
            $__viewVariables['listItemCart'] =  $this->getItemCart();
            $viewModel = new ViewModel($__viewVariables);
            $viewModel->setTerminal(true);

            return $viewModel;
        }
        
    }

    public function moreinfoAction() {
         $oService = $this->getServiceLocator();
        //$request = $this->getRequest();
        $articleDetailData = array();
        //if ($request->isXmlHttpRequest()) {
        $userId = 0;
        $this->layout()->showHeaderLinks = "NOT_LOGGED_IN";
        $oAuth = $this->getServiceLocator()->get('AuthService');
        if ($oAuth->hasIdentity()) {
            $userInfo = $oAuth->getIdentity();
            $userId = $userInfo->userId;
            $this->layout()->showHeaderLinks = "LOGGED_IN";
        }

        $oArticleCloseset = $oService->get('ArticleClosesetTable');
        $likedarticles = $oArticleCloseset->getLikedArticles($userId);
        $likearray = array();
        if(count($likedarticles)>0) {
           foreach($likedarticles as $likedarticle) {
                $likearray[] = $likedarticle['feeddataid'];
            }
        }
           $__viewVariables = array();
        $__viewVariables['likearray'] = $likearray;

        $oService = $this->getServiceLocator();
        $oFeedData = $oService->get('FeedDataTable');
        $oFeedMapping = $oService->get('FeedsMappingTable');
        //$feedDataId = $this->params()->fromRoute('feeddataid');
        $feedDataId = $_POST['id'];
        $feedDataDetail = $oFeedData->getItem($feedDataId);
        /*         * ***********block for inserting data into table when user view this article********** */
        $oArticleDetail = $oService->get('ArticleDetailTable');
        $articleId = $feedDataDetail['id'];
        $articleDetailData['feeddataid'] = $articleId;
        $articleDetailData['userId'] = $userId;
        $result = $oArticleDetail->getArticleDetailInfo($articleId, $userId);
        if (isset($result) && empty($result)) {
            $oArticleDetail->saveArticleDetailInfo($articleDetailData);
        }
        /**         * **********block for inserting data into table when user view this article********** */
        $aParams = array();
        $aParams['mappingType'] = 'store';
        $aParams['feedId'] = $feedDataDetail['feedId'];
        $storeDetail = $oFeedMapping->getFeedMapping($aParams);
     
        $__viewVariables['feedDataDetail'] = $feedDataDetail;
        $__viewVariables['feedMappingDetail'] = $storeDetail;

        $oAttributes = $oService->get('ProductAttributesTable');
        $aParams = array();
        $aParams['type'] = 'size';
        $aParams['productUID'] = $feedDataDetail['uid'];
        $__viewVariables['feedDataSizes'] = $oAttributes->getFeedDataSizes($aParams);
        $__viewVariables['listItemCart'] =  $this->getItemCart();
       // die(json_encode($__viewVariables));
         $viewModel = new ViewModel($__viewVariables);

          $viewModel->setTerminal(true);
        return $viewModel;
        // return $this->response;
        //}
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
        if (!$oAuth->getIdentity()) {
            return $this->redirect()->toUrl('/service');
        }

        $this->layout()->showSmallHeader = true;
        $this->layout()->showHeaderLinks = "NOT_LOGGED_IN";
        if ($oAuth->hasIdentity()) {
            $this->layout()->showHeaderLinks = "LOGGED_IN";
            $userInfo = $oAuth->getIdentity();
            $userId = $userInfo->userId;
            $this->layout()->firstName = $userInfo->firstName;
        } else {
            $this->redirect()->toRoute('auth');
        }

        if ($request->isPost()) {
            $posts = $request->getPost();
            $searchResult = $outfitTable->getOutfitsProducts(array('user_id' => $oAuth->getIdentity()->userId), $posts['searchkey']);
            $response->total = count($searchResult);
            $response->result = $searchResult;
        }

        if ($request->isXmlHttpRequest()) {
            die(\Zend\Json\Json::encode($response, true));
        }

        //return new ViewModel(array('outfits' => $outfitTable->getUsersOutfitsProducts(array('user_id' => $oAuth->getIdentity()->userId))));

			// grab the paginator from the AlbumTable
     		$paginator = $outfitTable->getUsersOutfitsProducts(array('user_id' => $oAuth->getIdentity()->userId));
     		// set the current page to what has been passed in query string, or to 1 if none set
     		$paginator->setCurrentPageNumber((int) $this->params()->fromQuery('page', 1));
     		// set the number of items per page to 10
     		$paginator->setItemCountPerPage(20);
     		    
			return new ViewModel(array(
         		'paginator' => $paginator
     		));
    
    }

    public function addOutfitAction() {
        $request = $this->getRequest();
        $oService = $this->getServiceLocator();
        $this->oSession = $oService->get('StorageService')->getSession();
        $outfitTable = $oService->get('OutfitsProductsTable');

        $id = $request->getQuery('id', 0);

        if ($request->isPost()) {
            $posts = $request->getPost();

            $oAuth = $this->getServiceLocator()->get('AuthService');
            $userInfo = $oAuth->getIdentity();
            $posts['user_id'] = $userInfo->userId;

            $saveExist = $posts['save_exit'];
            unset($posts['save_exit']);
            $id = $outfitTable->save($posts->toArray());
            
				$outfitMappingTable = $oService->get('OutfitsFeedsMappingTable');
            $outfitinfo = $outfitMappingTable->getOutfitsMapping(array('outfit_id' => $id));          
            if(count($outfitinfo) == 0)
            {
            	for($i=1; $i<=6; $i++)
            	{
            		$data['outfit_id'] = $id;
            		$data['feeds_uid'] = '';
                	$data['feed_position'] = $i;
                	$outfitMappingTable->save($data);
            	}
            }
            
            if ($saveExist == 'yes') {
                return $this->redirect()->toUrl('/service/outfits');
            } else {
                return $this->redirect()->toUrl('/service/createoutfit/?outfit=' . $id);
            }
        }

        $viewModel = new ViewModel(
                array('outfit' => current($outfitTable->getOutfitsProducts(array('outfit_id' => $id))))
        );
        if ($request->isXmlHttpRequest()) {
            $viewModel->setTerminal(true);
        }
        return $viewModel;
    }

    public function updateOutfitAction() {
        $request = $this->getRequest();
        $oService = $this->getServiceLocator();
        $outfitMappingTable = $oService->get('OutfitsFeedsMappingTable');
        if ($request->isPost()) {
            $posts = $request->getPost()->toArray();
            foreach ($posts['feedid'] as $index => $uid) {
                $data = array();
                $mapping = $outfitMappingTable->getOutfitsMapping(array('outfit_id' => $posts['outfit_id'], 'feed_position' => $index));
                //print_r($mapping); exit;
                if (isset($mapping[0]['id'])) {
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

    public function alloutfitsAction() {
        $outfitDetails = array();
        $oService = $this->getServiceLocator();
        $oFeedMapping = $oService->get('OutfitsFeedsMappingTable');
        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $request->getPost();
            $outfitArg = $data['filterArg'];

            if (isset($outfitArg) && !empty($outfitArg)) {
                $oProductsTable = $oService->get('OutfitsProductsTable');
                $outfitProductsTable = $oProductsTable->getOutfitsProducts(array(), null, true, $outfitArg['outfit']);
                if ($outfitProductsTable) {
                    $outfitDetails = $oFeedMapping->getOutfitsMapping(array('outfit_id' => $outfitProductsTable[0]['outfit_id']), true);
                    $__viewVariables['outfits'] = $outfitDetails;
                }
            } else {
                $oProductsTable = $oService->get('OutfitsProductsTable');
                $outfitProductsTable = $oProductsTable->getOutfitsProducts(array(), null, true);
                if ($outfitProductsTable) {
                    $outfitDetails = $oFeedMapping->getOutfitsMapping(array('outfit_id' => $outfitProductsTable[0]['outfit_id']), true);
                    $__viewVariables['outfits'] = $outfitDetails;
                }
            }

            $oAuth = $this->getServiceLocator()->get('AuthService');
            $userInfo = $oAuth->getIdentity();
            $userId = $userInfo->userId;

            $oArticleCloseset = $oService->get('ArticleClosesetTable');
            $likedarticles = $oArticleCloseset->getLikedArticles($userId);
            $likearray = array();
            if(count($likedarticles)>0) {
                foreach($likedarticles as $likedarticle) {
                    $likearray[] = $likedarticle['feeddataid'];
                }
            }
            $__viewVariables['likearray'] = $likearray;

            $viewModel = new ViewModel(array());
            $viewModel->setVariables(array('outfits' => $outfitDetails,'likearray' => $likearray));
            $viewModel->setTerminal(true);
            return $viewModel;
        }
    }

    public function createoutfitAction() {
        $oHelper = $this->getServiceLocator()->get('viewhelpermanager');
        $oHelper->get('HeadScript')->appendFile('/vendor/aciTree/js/jquery.aciPlugin.min.js');
        $oHelper->get('HeadScript')->appendFile('/vendor/aciTree/js/jquery.aciTree.dom.js');
        $oHelper->get('HeadScript')->appendFile('/vendor/aciTree/js/jquery.aciTree.core.js');
        $oHelper->get('HeadScript')->appendFile('/vendor/aciTree/js/jquery.aciTree.selectable.js');
        $oHelper->get('HeadScript')->appendFile('/vendor/aciTree/js/jquery.aciTree.checkbox.js');
        $oHelper->get('HeadScript')->appendFile('/vendor/aciTree/js/jquery.aciTree.radio.js');
        $oHelper->get('HeadScript')->appendFile('/vendor/jquery-ui.min.js');
        $oHelper->get('HeadScript')->appendFile('/js/ui/jquery.ui.core.js');
        $oHelper->get('HeadScript')->appendFile('/js/ui/jquery.ui.widget.js');
        $oHelper->get('HeadScript')->appendFile('/js/ui/jquery.ui.mouse.js');
        $oHelper->get('HeadScript')->appendFile('/js/ui/jquery.ui.draggable.js');
        $oHelper->get('HeadScript')->appendFile('/js/ui/jquery.ui.droppable.js');

        $oHelper->get('headLink')->appendStylesheet('/vendor/aciTree/css/aciTree.css');

        $__viewVariables = array();

        $this->layout()->showSmallHeader = true;

        $this->layout()->showHeaderLinks = "NOT_LOGGED_IN";
        $oAuth = $this->getServiceLocator()->get('AuthService');
        if ($oAuth->hasIdentity()) {
            $this->layout()->showHeaderLinks = "LOGGED_IN";
            $userInfo = $oAuth->getIdentity();
            $userId = $userInfo->userId;
            $this->layout()->firstName = $userInfo->firstName;
        } else {
            $this->redirect()->toRoute('auth');
        }

        $oService = $this->getServiceLocator();
        $oFeedData = $oService->get('FeedDataTable');

        $oFeedMapping = $oService->get('FeedsMappingTable');
        $oAuth = $oService->get('AuthService');

        $oArticleCloseset = $oService->get('ArticleClosesetTable');
        $likedarticles = $oArticleCloseset->getLikedArticles($userId);
        $likearray = array();
        if(count($likedarticles)>0) {
           foreach($likedarticles as $likedarticle) {
                $likearray[] = $likedarticle['feeddataid'];
            }
        }
        $__viewVariables['likearray'] = $likearray;

        $aPostParams = $this->params()->fromPost();
        if (isset($aPostParams['ArticlesPerPage'])) {
            $oAuth->getStorage()->set('service', array('ArticlesPerPage' => $aPostParams['ArticlesPerPage']));
        }

        $aGetParams = $this->params()->fromQuery();
        $aServiceData = $oAuth->getStorage()->get('service');
        $__viewVariables['articlesPerPage'] = (isset($aServiceData['ArticlesPerPage']) ? $aServiceData['ArticlesPerPage'] : 20);

        $paginator = $oFeedData->getFeedData($aPostParams, true, $userId, null, true);
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
        if (isset($aGetParams['outfit']) && $aGetParams['outfit']) {
            $outfitDetails = $oFeedMapping->getOutfitsMapping(array('outfit_id' => $aGetParams['outfit']), true);
            $__viewVariables['outfits'] = $outfitDetails;
            $__viewVariables['designer'] = true;
        } else {
            $oProductsTable = $oService->get('OutfitsProductsTable');
            $outfitProductsTable = $oProductsTable->getOutfitsProducts(array(), null, true);
            if ($outfitProductsTable) {
                $outfitDetails = $oFeedMapping->getOutfitsMapping(array('outfit_id' => $outfitProductsTable[0]['outfit_id']), true);
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
    
    
    //Function is used to get products on filter basis
    public function getcofeedsAction() {
        $request = $this->getRequest();
        $oAuth = $this->getServiceLocator()->get('AuthService');
        $userInfo = $oAuth->getIdentity();
        $userId = $userInfo->userId;
        if ($request->isXmlHttpRequest()) {
            $oService = $this->getServiceLocator();
            $oFeedData = $oService->get('FeedDataTable');

            $oArticleCloseset = $oService->get('ArticleClosesetTable');
            $likedarticles = $oArticleCloseset->getLikedArticles($userId);
            $likearray = array();
            if(count($likedarticles)>0) {
                foreach($likedarticles as $likedarticle) {
                    $likearray[] = $likedarticle['feeddataid'];
                }
            }
            $__viewVariables['likearray'] = $likearray;

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
            $paginator = $oFeedData->getFilterFeedData($aPostParams, true,$userId,null,'service');

            $oAttributes = $oService->get('ProductAttributesTable');
            $__viewVariables['attributes'] = $oAttributes->getAttributesTree(1);

            $__viewVariables['feedData'] = $paginator;

            $viewModel = new ViewModel($__viewVariables);
            $viewModel->setTerminal(true);
            return $viewModel;
            // return $this->response;
        }
    }

    public function addToCartAction(){
        $request = $this->getRequest();
        if ($request->isXmlHttpRequest()) {
            $oAuth = $this->getServiceLocator()->get('AuthService');
            $userInfo = $oAuth->getIdentity();
            $userId = $userInfo->userId;

            $feedId = $_POST['id'];
            // check feed_id compare with user_id is exist
            $cartTable = $this->getServiceLocator()->get('CartTable');
            $where = new Where();
            $where->equalTo('user_id',$userId)
                  ->AND 
                  ->equalTo('feed_id',$feedId);
            $flag = $cartTable->checkExist(['where' =>$where]);

            if($flag > 0) {
                // increment feed_id with user_id
                $cartTable->updateQuantity(['where'=>$where]);
            } else {
                // add new product
                $data = array(
                    'user_id'   => $userId,
                    'feed_id'   => $feedId,
                    'created_at' => date('Y-m-d H:i:s'),
                    'quantity'   => 1
                );

                $item_id = $cartTable->saveItem($data);
            }

            //get cart number of user
            // $where = new Where();
            // $where->equalTo('user_id',$userId);
            // $cartNumber = $cartTable->getCartNumber(['where'=>$where]);
            $cartNumber = $this->getCart();
            echo json_encode($cartNumber); die;
        }
    }  
    public function disableFromCartAction(){
        $request = $this->getRequest();
        if ($request->isXmlHttpRequest()) {
            $feedId = $_POST['id'];
            $cartTable = $this->getServiceLocator()->get('CartTable');
            $where = new Where();
            $where->equalTo('user_id',$this->userId)
                  ->AND 
                  ->equalTo('feed_id',$feedId);
            $cartTable->delete(['where'=>$where]);
               
            }
            $cartNumber = $this->getCart();
            echo json_encode($cartNumber); 
            die;
    }

    public function addOutfitToCartAction(){
        $request = $this->getRequest();
        if ($request->isXmlHttpRequest()) {
            $cartTable = $this->getServiceLocator()->get('CartTable');
            $oAuth = $this->getServiceLocator()->get('AuthService');
            $userInfo = $oAuth->getIdentity();
            $userId = $userInfo->userId;
            $outfit_id = $_POST['outfit_id'];
            if($outfit_id > 0) {
                $oFeedMapping = $this->getServiceLocator()->get('OutfitsFeedsMappingTable');
                $outfitDetails = $oFeedMapping->getOutfitsMapping(array('outfit_id' => $outfit_id), true);
                if(count($outfitDetails) > 0) {
                    foreach ($outfitDetails as $value) {
                        $feedId = $value['id'];

                         // check feed_id tương ưng vs user_id tôn tai chưa
                        $where = new Where();
                        $where->equalTo('user_id',$userId)
                              ->AND 
                              ->equalTo('feed_id',$feedId);
                        $flag = $cartTable->checkExist(['where' =>$where]);

                        if($flag > 0) {
                            continue;
                        } else {

                            // thêm sản phẩm mới
                            $data = array(
                                'user_id'   => $userId,
                                'feed_id'   => $feedId,
                                'created_at' => date('Y-m-d H:i:s'),
                                'quantity'   => 1
                            );

                            $item_id = $cartTable->saveItem($data);
                        }
                    }
                }
            }
           

            $cartNumber = $this->getCart();
            echo json_encode($cartNumber); die;
        }
    }
    public function deleteCartAction(){
        $request = $this->getRequest();
        if ($request->isXmlHttpRequest()) {
            $feedId = $_POST['id'];
            // check feed_id tương ưng vs user_id tôn tai chưa
            $cartTable = $this->getServiceLocator()->get('CartTable');
            $where = new Where();
            $where->equalTo('user_id',$this->userId)
                  ->AND 
                  ->equalTo('feed_id',$feedId);
             $cartTable->delete(['where' =>$where]);
            die(json_encode(array('valid' =>true)));
          
        }
        die(json_encode(array('valid' =>false)));
    }

    public function cartAction(){
        //$pre_url = $this->getRequest()->getHeader('Referer')->getUri();
        $pre_url = $this->preUrl();
        $cartTable = $this->getServiceLocator()->get('CartTable');

        $where = new Where();
        $where->equalTo('user_id',$this->userId);

        $arrParam = array(
            'where' => $where,
            'join' => array(
                'FeedData' =>
                    array(
                       'table'=> 'FeedData',
                       'on'   => 'FeedData.id = carts.feed_id',
                       'type' => 'INNER',
                       'field' => array(
                           'name'  => 'name',
                           'brand' => 'brand',
                           'price' => 'price',
                           'imageurl' => 'imageurl',
                           'currency' => 'currency',
                           'color' =>'color'
                        )
                    ),
            )
        );
        $listItem = $cartTable->getCart($arrParam);
        return new ViewModel([
            'listItem'=>$listItem,
            'pre_url' => $pre_url
        ]);

    }

    public function getCartNumberAction(){
        $cartNumber = $this->getCart();
        echo json_encode($cartNumber); die;
    }

    public function outfitDetailsAjaxAction() {
        $request = $this->getRequest();
        $oService = $this->getServiceLocator();
        $pre_url = '';
        if ($request->isXmlHttpRequest()) { 
            $outfit_id = $_POST['outfit_id'];
            //check outfit closet
            $outfitClosesetTable = $oService->get('OutfitClosesetTable');
            $isLike = $outfitClosesetTable->getArticleLikes($outfit_id,$this->userId);
        
            $oFeedMapping = $oService->get('OutfitsFeedsMappingTable');
            $outfitDetails = $oFeedMapping->getOutfitsMapping(array('outfit_id' => $outfit_id), true);
    
            $oArticleCloseset = $oService->get('ArticleClosesetTable');
            $likedarticles = $oArticleCloseset->getLikedArticles($this->userId);
            $likearray = array();
            if(count($likedarticles) > 0) {
               foreach($likedarticles as $likedarticle) {
                    $likearray[] = $likedarticle['feeddataid'];
                }
            }
            $viewModel = new ViewModel([
                'outfits'=>$outfitDetails,
                'likearray'=>$likearray,
                'isLike' =>$isLike,
                'outfit_id' => base64_encode($outfit_id),
                'pre_url' => $pre_url,
                'listItemCart' => $this->getItemCart(),
            ]);
            $viewModel->setTerminal(true);
            return $viewModel;
        }
    }

    public function outfitDetailsAction(){
        $pre_url = $this->preUrl();
        if(!$this->userId) {
            $this->redirect()->toUrl("/auth");
        }
        $oService = $this->getServiceLocator();
        $outfit_id = $this->params('id');
        if($outfit_id > 0 ) {
            //check outfit closet
            $outfitClosesetTable = $oService->get('OutfitClosesetTable');
            $isLike = $outfitClosesetTable->getArticleLikes($outfit_id,$this->userId);
        
            $oFeedMapping = $oService->get('OutfitsFeedsMappingTable');
            $outfitDetails = $oFeedMapping->getOutfitsMapping(array('outfit_id' => $outfit_id), true);
    
            $oArticleCloseset = $oService->get('ArticleClosesetTable');
            $likedarticles = $oArticleCloseset->getLikedArticles($this->userId);
            $likearray = array();
            if(count($likedarticles) > 0) {
               foreach($likedarticles as $likedarticle) {
                    $likearray[] = $likedarticle['feeddataid'];
                }
            }
            return new ViewModel([
                'outfits'=>$outfitDetails,
                'likearray'=>$likearray,
                'isLike' =>$isLike,
                'outfit_id' => base64_encode($outfit_id),
                'pre_url' => $pre_url,
                'listItemCart' => $this->getItemCart(),
            ]);
        } else {
            $this->redirect()->toUrl("/service");
        }
    }

    public function outfitInlineAction(){
        if(!$this->userId) {
            $this->redirect()->toUrl("/auth");
        }
        $oService = $this->getServiceLocator();
        $outfit_id = $this->params('id');
        if($outfit_id > 0 ) {
            $oFeedMapping = $oService->get('OutfitsFeedsMappingTable');
            $outfitDetails = $oFeedMapping->getOutfitsMapping(array('outfit_id' => $outfit_id), true);
            $__viewVariables['outfits'] = $outfitDetails;
            $__viewVariables['outfit_id']  = $outfit_id;
        
            return new ViewModel($__viewVariables);
        } else {
            $this->redirect()->toUrl("/service");
        }
    }



}
