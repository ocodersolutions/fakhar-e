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
use Zend\Json\Json;
use Zend\View\Helper\ServerUrl;
use Zend\Session\Container;
use \Zend\Db\Sql\Where;
use Ocoder\Base\BaseActionController;


class ServiceController extends BaseActionController {


    public function indexAction() {
        $oAuth = $this->getServiceLocator()->get('AuthService');
        $oHelper = $this->getServiceLocator()->get('viewhelpermanager');
        $oHelper->get('HeadScript')->appendFile('/vendor/aciTree/js/jquery.aciPlugin.min.js');
        $oHelper->get('HeadScript')->appendFile('/vendor/aciTree/js/jquery.aciTree.dom.js');
        $oHelper->get('HeadScript')->appendFile('/vendor/aciTree/js/jquery.aciTree.core.js');
        $oHelper->get('HeadScript')->appendFile('/vendor/aciTree/js/jquery.aciTree.selectable.js');
        $oHelper->get('HeadScript')->appendFile('/vendor/aciTree/js/jquery.aciTree.checkbox.js');
        $oHelper->get('HeadScript')->appendFile('/vendor/aciTree/js/jquery.aciTree.radio.js');
        $oHelper->get('HeadScript')->appendFile('/vendor/jquery-ui.min.js');
        $oHelper->get('HeadScript')->appendFile('/js/jquery.hc-sticky.min.js');
        $oHelper->get('headLink')->appendStylesheet('/vendor/aciTree/css/aciTree.css');
        // $oHelper->get('HeadScript')->appendFile('/public/vendor/aciTree/js/jquery.aciPlugin.min.js');
        // $oHelper->get('HeadScript')->appendFile('/public/vendor/aciTree/js/jquery.aciTree.dom.js');
        // $oHelper->get('HeadScript')->appendFile('/public/vendor/aciTree/js/jquery.aciTree.core.js');
        // $oHelper->get('HeadScript')->appendFile('/public/vendor/aciTree/js/jquery.aciTree.selectable.js');
        // $oHelper->get('HeadScript')->appendFile('/public/vendor/aciTree/js/jquery.aciTree.checkbox.js');
        // $oHelper->get('HeadScript')->appendFile('/public/vendor/aciTree/js/jquery.aciTree.radio.js');
        // $oHelper->get('HeadScript')->appendFile('/public/vendor/jquery-ui.min.js');
        // $oHelper->get('HeadScript')->appendFile('/public/js/jquery.hc-sticky.min.js');
        // $oHelper->get('headLink')->appendStylesheet('/public/vendor/aciTree/css/aciTree.css');

        //$oHelper->get('HeadScript')->appendFile('/js/ui/jquery.ui.core.js');
        //$oHelper->get('HeadScript')->appendFile('/js/ui/jquery.ui.widget.js');
        //$oHelper->get('HeadScript')->appendFile('/js/ui/jquery.ui.mouse.js');
        //$oHelper->get('HeadScript')->appendFile('/js/ui/jquery.ui.draggable.js');
        //$oHelper->get('HeadScript')->appendFile('/js/ui/jquery.ui.droppable.js');

        

        $__viewVariables = array();
        $aGetParams = $this->params()->fromQuery();

        // var_dump($this->layout()); die;
        
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
           
            $outfitDetails_main = $oFeedMapping->getOutfitsMapping(array('outfit_id' => $aGetParams['outfit']), true);
            //$__viewVariables['outfits'] = $outfitDetails;
            $__viewVariables['designer'] = true;

            $oProductsTable = $oService->get('OutfitsProductsTable');
            
            if( !empty($oAuth->getIdentity()->defaultVenue) ) {
                $__viewVariables['defaultVenue'][] = $oAuth->getIdentity()->defaultVenue;
            }

            list($outfitProducts, $__viewVariables['outfits_count'], $__viewVariables['outfits_offset']) = $oProductsTable->getOutfitsProducts(array(), null, 0, $__viewVariables['defaultVenue'], array('returnCount'=>true, 'outfit_id'=>$aGetParams['outfit']));
            if ($outfitProducts) {
                $__viewVariables['outfitsData'] = $outfitProducts; 
                $i = 0;
                foreach($outfitProducts as $outfitProductsTable) {
                    if($i == 1) {
                        $__viewVariables['outfits'][] = $outfitDetails_main;
                    }
                    $outfitDetails = $oFeedMapping->getOutfitsMapping(array('outfit_id' => $outfitProductsTable['outfit_id']), true);
                    $__viewVariables['outfits'][] = $outfitDetails;
                    $i++;
                }
            }
        } else {
            $oProductsTable = $oService->get('OutfitsProductsTable');
            
            if( !empty($oAuth->getIdentity()->defaultVenue) ) {
                $__viewVariables['defaultVenue'][] = $oAuth->getIdentity()->defaultVenue;
            }

            list($outfitProducts, $__viewVariables['outfits_count'], $__viewVariables['outfits_offset']) = $oProductsTable->getOutfitsProducts(array(), null, 0, $__viewVariables['defaultVenue'], array('returnCount'=>true));
            if ($outfitProducts) {
                $__viewVariables['outfitsData'] = $outfitProducts; 
                foreach($outfitProducts as $outfitProductsTable) {
                    $outfitDetails = $oFeedMapping->getOutfitsMapping(array('outfit_id' => $outfitProductsTable['outfit_id']), true);
                    $__viewVariables['outfits'][] = $outfitDetails;
                }
            }
        }
        $request = $this->getRequest();
        if ($request->isXmlHttpRequest()) {
            $viewModel = new ViewModel();
            $viewModel->setTerminal(true);
        }

 
        $__viewVariables['defaultView'] = isset($aGetParams['dv']) && !empty($aGetParams['dv']) ? 'article' : 'outfit';
        
        return $__viewVariables;
    }
    
    public function shareOutfitAction() {
        $oHelper = $this->getServiceLocator()->get('viewhelpermanager');
        $oHelper->get('HeadScript')->appendFile('/vendor/aciTree/js/jquery.aciPlugin.min.js');
        $oHelper->get('HeadScript')->appendFile('/vendor/aciTree/js/jquery.aciTree.dom.js');
        $oHelper->get('HeadScript')->appendFile('/vendor/aciTree/js/jquery.aciTree.core.js');
        $oHelper->get('HeadScript')->appendFile('/vendor/aciTree/js/jquery.aciTree.selectable.js');
        $oHelper->get('HeadScript')->appendFile('/vendor/aciTree/js/jquery.aciTree.checkbox.js');
        $oHelper->get('HeadScript')->appendFile('/vendor/aciTree/js/jquery.aciTree.radio.js');
        $oHelper->get('HeadScript')->appendFile('/vendor/jquery-ui.min.js');
        $oHelper->get('HeadScript')->appendFile('/js/jquery.hc-sticky.min.js');
        $oHelper->get('headLink')->appendStylesheet('/vendor/aciTree/css/aciTree.css');
    
    
        $this->layout()->hideFooter = true;
    
        $__viewVariables = array();
        $aGetParams = $this->params()->fromQuery();
        
        $oService = $this->getServiceLocator();
        $oFeedMapping = $oService->get('OutfitsFeedsMappingTable');
    
                $outfitDetails_main = $oFeedMapping->getOutfitsMapping(array('outfit_id' => $aGetParams['outfit']), true);
            //$__viewVariables['outfits'] = $outfitDetails;
            $__viewVariables['designer'] = true;

            $oProductsTable = $oService->get('OutfitsProductsTable');

            list($outfitProducts, $__viewVariables['outfits_count'], $__viewVariables['outfits_offset']) = $oProductsTable->getOutfitsProducts(array(), null, 0, null, array('returnCount'=>true));
            if ($outfitProducts) {
                $__viewVariables['outfitsData'] = $outfitProducts;
                $i = 0;
                foreach($outfitProducts as $outfitProductsTable) {
                    if($i == 1) {
                        $__viewVariables['outfits'][] = $outfitDetails_main;
                    }
                    $outfitDetails = $oFeedMapping->getOutfitsMapping(array('outfit_id' => $outfitProductsTable['outfit_id']), true);
                    $__viewVariables['outfits'][] = $outfitDetails;
                    $i++;
                }
            }

                        return $__viewVariables;
    }    

    public function clearfilterAction(){

        $oAuth = $this->getServiceLocator()->get('AuthService');
        $__viewVariables = array();
        $userId = null;
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


        $aPostParams = $this->params()->fromPost();
        if (isset($aPostParams['ArticlesPerPage'])) {
            $oAuth->getStorage()->set('service', array('ArticlesPerPage' => $aPostParams['ArticlesPerPage']));
        }

        $aGetParams = $this->params()->fromQuery();
        $aServiceData = $oAuth->getStorage()->get('service');
        $__viewVariables['articlesPerPage'] = (isset($aServiceData['ArticlesPerPage']) ? $aServiceData['ArticlesPerPage'] : PRODUCTS_PER_PAGE);
        
        $paginator = $oFeedData->getFeedData($aPostParams, true, $userId, null, true);
        $paginator->setCurrentPageNumber((isset($aGetParams['page']) ? $aGetParams['page'] : 1));
        $paginator->setItemCountPerPage($__viewVariables['articlesPerPage']);
        // //print_r($paginator->getPages());


        $__viewVariables['feedData'] = $paginator;
        $__viewVariables['pages'] = $paginator->getPages();
   
        // $viewModel = new ViewModel();
        // $viewModel->setTemplate('partial/rightbox.phtml');
      
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
                if (isset($outfitLikes) && !empty($outfitLikes)) {
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
                    $status['already'] = "Y";
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
    
    public function unlikeclossetAction() {
        
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

                $articleLikes = $oArticleData->unLikeArticleLike($articleId, $userId);
                $status['status'] = "Y";
                $status['mgs'] = "Article removed successfully!";
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
        $this->layout('layout/empty_with_css.phtml');
        
        $this->layout()->showHeaderLinks = "NOT_LOGGED_IN";
        $oAuth = $this->getServiceLocator()->get('AuthService');
        // if ($oAuth->hasIdentity()) {
        //     $this->layout()->showHeaderLinks = "LOGGED_IN";

        // }
        $id = $this->params('feeddataid');

       
        $feedId = base64_decode($id);
        $oService = $this->getServiceLocator();
        $oFeedData = $oService->get('FeedDataTable');
        $feedDataDetail = $oFeedData->getItem($feedId);

       

        $oFeed = $oService->get('FeedsTable');
         $__viewVariables['feedInfo'] = $oFeed->getFeedByID( $feedDataDetail['feedId'] );
               
        $status = array();
        $oAuth = $this->getServiceLocator()->get('AuthService');
        $userInfo = $oAuth->getIdentity();
        if ($oAuth->hasIdentity()) {
            $this->layout()->showHeaderLinks = "LOGGED_IN";
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


//             $buyUrl =  $feedDataDetail ['buyurl'];
//             $base_url = explode('url',$buyUrl)[0];
//             $str_replace = strstr($buyUrl,'url');
//             $str_add = 'SID='.$userId.'_'.$feedId.'&'.$str_replace;
//             $feedDataDetail['buyurl'] =  $base_url.$str_add;
//             echo $feedDataDetail['buyurl'] . "<br \>";
//               $feedDataDetail['buyurl'] = str_replace('?url=', "?SID={$userId}_{$feedId}&url=", $feedDataDetail['buyurl']);
//             echo $feedDataDetail['buyurl'] . "<br \>";
//             die;

            //save log_redirect
            $arr_logs = [
                'user_id' => $userId,
                'action' => 'buy',
                'time' => date('Y-m-d H:i:s'),
                'ip' => $this->get_client_ip(),
                'country' => $this->ip_info($this->get_client_ip(), "Country"),
                'browser' => $this->getBrowser()['name'],
                'feed_id' => $feedId,
            ];

            $logRedirectTable = $this->getServiceLocator()->get('LogRedirectTable');

            $logId = $logRedirectTable->saveItem($arr_logs);
            $feedDataDetail['buyurl'] = str_replace('?url=', "?SID={$logId}&url=", $feedDataDetail['buyurl']);
            //echo $feedDataDetail['buyurl']; die;
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

            $viewModel = new ViewModel($__viewVariables);
            $viewModel->setTerminal(true);

                 //save log
            $log_arr = array();
            $except_arr = ['limit','deals','stores','page','offset'];
            foreach ($aPostParams as $key => $value) {
                foreach ($except_arr as  $value) {
                    if($value == $key) {
                        unset($aPostParams[$key]);
                    }
                }
            }
            $aPostParams['ip'] = $this->get_client_ip();
            $aPostParams['country'] = $this->ip_info($this->get_client_ip(), "Country");
            $aPostParams['user_id'] = $userId;
            $aPostParams['time'] = date('Y-m-d H:i:s');
            $logTable = $this->getServiceLocator()->get('LogTable');

            $logTable->saveItem($aPostParams);
            return $viewModel;
            // return $this->response;
        }
    }

    //Function is used to show detail of feed data

    public function feeddatadetailAction() {
        $request = $this->getRequest();
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

        $oService = $this->getServiceLocator();
        $oFeedData = $oService->get('FeedDataTable');
        $oFeedMapping = $oService->get('FeedsMappingTable');
        $feedDataId = $this->params()->fromRoute('feeddataid');
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
        $__viewVariables['feedDataDetail'] = $feedDataDetail;
        $__viewVariables['feedMappingDetail'] = $storeDetail;
        
        $oArticleClosetData = $oService->get('ArticleClosesetTable');
        $articleLikes = $oArticleClosetData->getArticleLikes($articleId, $userId);
        if (isset($articleLikes) && !empty($articleLikes)) {
        		$__viewVariables['articlecloset'] = 'Y';
        } else {
				$__viewVariables['articlecloset'] = 'N';
        }

        list($width, $height, $type, $attr) = getimagesize($__viewVariables['feedDataDetail']['imageurl']);
        $this->layout()->additionalMeta = '
                    <meta property="og:image" content="'.$__viewVariables['feedDataDetail']['imageurl'].'" />
                    <meta property="og:title" content="Lnove" />
                    <meta property="og:site_name" content="Lnove"/>
                    <meta property="og:url" content="http://'.URL_APPLICATION.'/service/feeddatadetail/'.$feedDataId.'" /> 
                    <meta property="og:description" content="'.$__viewVariables['feedDataDetail']['description'].'" />
                    <meta property="og:image:width" content="'.$width.'" />
                    <meta property="og:image:height" content="'.$height.'" />
                ';  
        
        $oAttributes = $oService->get('ProductAttributesTable');
        $aParams = array();
        $aParams['type'] = 'size';
        $aParams['productUID'] = $feedDataDetail['uid'];
        $__viewVariables['feedDataSizes'] = $oAttributes->getFeedDataSizes($aParams);
        $viewModel = new ViewModel($__viewVariables);
        $viewModel->setTerminal(true);
        //return $viewModel;
        return $__viewVariables;
        // return $this->response;
        //}
    }

     //Function is used to show detail of feed data
    public function getfeeddetailAction(){
        if ($this->getRequest()->isXmlHttpRequest()) {
            $oService = $this->getServiceLocator();
            $oFeedData = $oService->get('FeedDataTable');
            $oFeedMapping = $oService->get('FeedsMappingTable');
            $feedDataId = $this->getRequest()->getPost()['feeddataid'];
            //$feedDataId = $this->params()->fromRoute('feeddataid');
            $feedDataDetail = $oFeedData->getItem($feedDataId);
            /*         * ***********block for inserting data into table when user view this article********** */
            $oArticleDetail = $oService->get('ArticleDetailTable');
            $articleId = $feedDataDetail['id'];
            $articleDetailData['feeddataid'] = $articleId;

            $userId = null;
               $oAuth = $this->getServiceLocator()->get('AuthService');
            if ($oAuth->hasIdentity()) {
                $this->layout()->showHeaderLinks = "LOGGED_IN";
                $userInfo = $oAuth->getIdentity();
                $userId = $userInfo->userId;
                $this->layout()->firstName = $userInfo->firstName;
            } else {
                $this->redirect()->toRoute('auth');
            }

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
            $__viewVariables['feedDataDetail'] = $feedDataDetail;
            $__viewVariables['feedMappingDetail'] = $storeDetail;
            
            $oArticleClosetData = $oService->get('ArticleClosesetTable');
            $articleLikes = $oArticleClosetData->getArticleLikes($articleId, $userId);
            if (isset($articleLikes) && !empty($articleLikes)) {
                 $__viewVariables['articlecloset'] = 'Y';
            } else {
                    $__viewVariables['articlecloset'] = 'N';
            }

            $oAttributes = $oService->get('ProductAttributesTable');
            $aParams = array();
            $aParams['type'] = 'size';
            $aParams['productUID'] = $feedDataDetail['uid'];
            $__viewVariables['feedDataSizes'] = $oAttributes->getFeedDataSizes($aParams);
            
            $oArticleCloseset = $oService->get('ArticleClosesetTable');
            $likedarticles = $oArticleCloseset->getLikedArticles($userId);
            $likearray = array();
            if(count($likedarticles)>0) {
               foreach($likedarticles as $likedarticle) {
                    $likearray[] = $likedarticle['feeddataid'];
                }
            }
            $__viewVariables['likearray'] = $likearray;

            $viewModel = new ViewModel($__viewVariables);
            $viewModel->setTerminal(true);
            return  $viewModel;
        }
    }

    public function outfitsAction() {
        $oHelper = $this->getServiceLocator()->get('viewhelpermanager');
        $oHelper->get('HeadScript')->appendFile('/fancybox/source/jquery.fancybox.js');
        $oHelper->get('headLink')->appendStylesheet('/fancybox/source/jquery.fancybox.css');
        $oHelper->get('HeadScript')->appendFile('/js/jquery.datetimepicker.js');
        $oHelper->get('headLink')->appendStylesheet('/css/jquery.datetimepicker.css');
        
        $__viewVariables = array();
        
        $aGetParams = $this->params()->fromQuery();
        if( isset($aGetParams['searchkey']) ) {
            $__viewVariables['searchkey'] = $aGetParams['searchkey'];
        }
        
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
        if ($oAuth->hasIdentity() && $oAuth->getIdentity()->userType==2) {
            $this->layout()->showHeaderLinks = "LOGGED_IN";
            $userInfo = $oAuth->getIdentity();
            $userId = $userInfo->userId;
            $this->layout()->firstName = $userInfo->firstName;
        } else {
            $this->redirect()->toRoute('auth');
        }
        
        if( $oAuth->getIdentity()->userType == '2' ) {
            $aCond = array();
        }
        else {
            $aCond = array('user_id' => $oAuth->getIdentity()->userId);
        }        

        if ($request->isPost()) {
            $posts = $request->getPost();
            $searchResult = $outfitTable->getOutfitsProducts($aCond, $posts['searchkey']);
            $response->total = count($searchResult);
            $response->result = $searchResult;
        }

        if ($request->isXmlHttpRequest()) {
            die(\Zend\Json\Json::encode($response, true));
        }

        //return new ViewModel(array('outfits' => $outfitTable->getUsersOutfitsProducts(array('user_id' => $oAuth->getIdentity()->userId))));



     		$paginator = $outfitTable->getUsersOutfitsProducts( $aCond );
     		// set the current page to what has been passed in query string, or to 1 if none set
     		$paginator->setCurrentPageNumber((int) $this->params()->fromQuery('page', 1));
     		// set the number of items per page to 10
     		$paginator->setItemCountPerPage(20);
     	
     		$__viewVariables['paginator'] = $paginator;
     		    
			return $__viewVariables;
    
    }

    public function addOutfitAction() {

        $request = $this->getRequest();
        $oService = $this->getServiceLocator();
        $this->oSession = $oService->get('StorageService')->getSession();
        $outfitTable = $oService->get('OutfitsProductsTable');
        
        $oAuth = $this->getServiceLocator()->get('AuthService');
        if( !$oAuth->hasIdentity() ) {
            $this->redirect()->toRoute('auth');
        }

        $id = $request->getQuery('id', 0);

        if ($request->isPost()) {
            $posts = $request->getPost();

            
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
        if($id > 0) {
            $viewModel = new ViewModel(
                array('outfit' => current($outfitTable->getOutfitsProducts(array('outfit_id' => $id))))
            );
        } else {
            $viewModel = new ViewModel();
        }
        

        if ($request->isXmlHttpRequest()) {
            $viewModel->setTerminal(true);
        }
        return $viewModel;
    }

    public function cloneOutfitAction(){
        $request = $this->getRequest();
        $oService = $this->getServiceLocator();
        $oProductsTable = $oService->get('OutfitsProductsTable');
        $oFeedMapping = $oService->get('OutfitsFeedsMappingTable');
        $id = $request->getQuery('id');
        if($id > 0) {
            // duplicate 
            $where = new Where();
            $where->equalTo('outfit_id',$id);
            $item = $oProductsTable->getItem($where);
            unset($item['outfit_id']);
            $new_id = $oProductsTable->save($item);
       
            // make copy product with new id
            $listFeedMapping = $oFeedMapping->listItem($where);
            if(count($listFeedMapping) > 0) {
                foreach ($listFeedMapping as $key => $value) {
                    unset($value['id']);
                    $value['outfit_id'] = $new_id;
                    $oFeedMapping->save($value);
                }
            }

            @copy(PUBLIC_PATH."/feed/outfits/{$id}.jpg", PUBLIC_PATH."/feed/outfits/{$new_id}.jpg");
        }

        return $this->redirect()->toUrl('/service/outfits?searchkey='.$new_id);
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
            $outfitMappingTable->generateOutfitImage( $posts['outfit_id'] );
            //($posts); exit;
            if(isset($posts['save'])) {
                return $this->redirectBack();
            }
        }

        return $this->redirect()->toUrl('/service/outfits');
    }

    /*
        redirect previous page
    */
    public function redirectBack(){
        $url = $this->getRequest()->getHeader('Referer')->getUri();
        return $this->redirect()->toUrl($url);
    }

    public function removeOutfitAction() {
        $request = $this->getRequest();
        $oService = $this->getServiceLocator();
        $outfitTable = $oService->get('OutfitsProductsTable');
        $oFeedMapping = $oService->get('OutfitsFeedsMappingTable');

        $id = $request->getQuery('id', 0);
        $outfitTable->remove($id);
        
        $where = new Where();
        $where->equalTo('outfit_id',$id);
        $oFeedMapping->removeByCondition($where);
        return $this->redirect()->toUrl('/service/outfits');
    }

    public function alloutfitsAction() {

        $outfitDetails = array();
        $oService = $this->getServiceLocator();
        $oFeedMapping = $oService->get('OutfitsFeedsMappingTable');
        $request = $this->getRequest();

        //$outfitArg = ['outfit'=>array('Night Out'), 'outfits_offset'=>3];
        if ($request->isPost()) {
            $data = $request->getPost();
            $outfitArg = $data['filterArg'];
            //die(json_encode($outfitArg));
            if (isset($outfitArg) && !empty($outfitArg)) { 
                $oProductsTable = $oService->get('OutfitsProductsTable');
                list($outfitProducts, $__viewVariables['outfits_count'], $__viewVariables['outfits_offset']) = $oProductsTable->getOutfitsProducts(array(), null, $outfitArg['outfits_offset'], $outfitArg['outfit'], array('returnCount'=>true));
              
                if ($outfitProducts) {
                    $__viewVariables['outfitsData'] = $outfitProducts;
                    foreach($outfitProducts as $outfitProductsTable) {
                        $outfitDetails = $oFeedMapping->getOutfitsMapping(array('outfit_id' => $outfitProductsTable['outfit_id']), true);
                        $__viewVariables['outfits'][] = $outfitDetails;
                    }
                    // echo '<pre style="color:blue">';
                    // var_dump($outfitProducts);
                    // echo '</pre>';    
                    // die(__FILE__);
                }
            } else { $__viewVariables['test'][] = 'venue no';
                $oProductsTable = $oService->get('OutfitsProductsTable');
                list($outfitProducts, $__viewVariables['outfits_count'], $__viewVariables['outfits_offset']) = $oProductsTable->getOutfitsProducts(array(), null, $outfitArg['outfits_offset'], $__viewVariables['defaultVenue'], array('returnCount'=>true));
                if ($outfitProducts) {
                    $__viewVariables['outfitsData'] = $outfitProducts;
                    foreach($outfitProducts as $outfitProductsTable) {
                        $outfitDetails = $oFeedMapping->getOutfitsMapping(array('outfit_id' => $outfitProductsTable['outfit_id']), true);
                        $__viewVariables['outfits'][] = $outfitDetails;
                    }
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

            
        }

        $viewModel = new ViewModel(array());
        $viewModel->setVariables( $__viewVariables );
        $viewModel->setTerminal(true);        
        return $viewModel;
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
        $userId='';
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
            foreach($outfitDetails as &$outfit) {
                $outfit['item_count'] = $oFeedData->getItemByUID( $outfit['feeds_uid'] );
                $outfit['item_count'] = $outfit['item_count']['item_count'];
            }
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
            $aGetParams = $this->params()->fromQuery();
            
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
            
            if( isset($aGetParams['requestFrom']) && $aGetParams['requestFrom']== 'create_outfit' )  {
                $paginator = $oFeedData->getFilterFeedData($aPostParams, true);
            }
            else {
                $paginator = $oFeedData->getFilterFeedData($aPostParams, true,$userId,null,'service');
            }
                        
            

            $oAttributes = $oService->get('ProductAttributesTable');
            $__viewVariables['attributes'] = $oAttributes->getAttributesTree(1);

            $__viewVariables['feedData'] = $paginator;

            $viewModel = new ViewModel($__viewVariables);
            $viewModel->setTerminal(true);

            return $viewModel;
            // return $this->response;
        }
    }    


}
