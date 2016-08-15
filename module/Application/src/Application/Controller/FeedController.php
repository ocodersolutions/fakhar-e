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
use Zend\Paginator\Paginator;
use Zend\View\Renderer\PhpRenderer;
use Ocoder\Base\BaseActionController;

class FeedController extends BaseActionController
{
    protected $_tableGateway; // default TableGateway variable

    private function getTable($tableName) {
        if (empty($this->_tableGateway)) {
            $this->_tableGateway = $this->getServiceLocator()->get($tableName);
        }
    }
    public function indexAction()
    {
        $root_dir = realpath('.').'/public/feed/';
    	
    	$oService = $this->getServiceLocator();
    	$oFeedData = $oService->get('FeedDataTable');
    	
    	$programDir = $this->getEvent()->getRouteMatch()->getParam('pname');
    	$imageId = $this->getEvent()->getRouteMatch()->getParam('image');
        
    	$imageId = preg_replace("/[^0-9]/", "", $imageId );
    	
    	$aProductData = $oFeedData->getItem( $imageId );
    	$outPutImage = $root_dir.'/'.$programDir.'/'.$imageId;
    	if( !is_dir($root_dir.'/'.$programDir) ) mkdir($root_dir.'/'.$programDir,0777,true);
    	
    	$mime_type = exif_imagetype( $aProductData['imageurl'] );
        
    	$mime_type = image_type_to_mime_type($mime_type);
    	
    	switch ($mime_type){
    		case "image/jpeg": $sExtension = 'jpg'; break;
    		case "image/png": $sExtension = 'gif'; break;
    		case "image/gif": $sExtension = 'png'; break;
    	}    

    	$outPutImage = $outPutImage . '.' . $sExtension;
        //$fp = fopen($outPutImage, "w");
        //fwrite($fp, file_get_contents($aProductData['imageurl']));
        //fclose($fp);
    	$command = "convert \"{$aProductData['imageurl']}\"  -resize 245x300 -size 245x300 xc:white +swap -gravity center  -composite {$outPutImage}";
    	exec($command);
        
    	switch ($mime_type){
    		case "image/jpeg":
    			header('Content-Type: image/jpeg');
    			$img = imagecreatefromjpeg($outPutImage);
    			imagejpeg($img);
    			break;
    		case "image/png":
    			header('Content-Type: image/png');
    			$img = imagecreatefrompng($outPutImage);
    			$background = imagecolorallocate($img, 0, 0, 0);
    			imagecolortransparent($img, $background);
    			imagealphablending($img, false);
    			imagesavealpha($img, true);
    			imagepng($img);
    			break;
    		case "image/gif":
    			header('Content-Type: image/gif');
    			$img = imagecreatefromgif($outPutImage);
    			$background = imagecolorallocate($img, 0, 0, 0);
    			imagecolortransparent($img, $imageId);
    			imagegif($img);
    			break;
    	}
    	 
    	$oFeedData->updateLocalImage($imageId, $imageId.'.'.$sExtension);
    	
    	// Free up memory
    	imagedestroy($img);
    }
    
    public function processInvalidOutfitsAction()
    {
        $oHelper = $this->getServiceLocator()->get('viewhelpermanager');
        $oService = $this->getServiceLocator();
        $outfitTable = $oService->get('OutfitsProductsTable');
        $outfitMissing = $outfitTable->getOutfitsProductsMissing();

        $configTable = $this->getTable('ConfigTable');

        $listUser = $this->_tableGateway->getUserEmailNotification(array('where'=>array('config_key'=>'OutFitNotificationEmail')))->toArray(); 

       
       $mailService = $this->sendMail();

      $this->renderer = new PhpRenderer(); //$this->getServiceLocator()->get('ViewRenderer');
      //echo realpath(__DIR__.'/../../../view/application/email'); die;
      $this->renderer->resolver()->addPath( realpath(__DIR__.'/../../../view/application/email') ); 
      
        $oEmailService = $this->getServiceLocator()->get('Application\Service\EmailService');
          $layout = new ViewModel([
              'outfitMissing' => $outfitMissing
            ]);
            $layout->setTemplate('template');
            
            $content = $this->renderer->render($layout); 
            $oEmailService->setSubject('Email Notification.');
            $oEmailService->setHtmlBody($content);
            
            foreach ($listUser as  $value) {
                $oEmailService->setTo($value['config_value']);
                $oEmailService->send();
            }
            

        
        die('done');
    }
    
    public function processCJTestAction()
    { //echo "OK \n"; die;
            $aGetParams = $this->params()->fromQuery();
            $iFeedId = $aGetParams['feed_id'];
            $oService = $this->getServiceLocator();
            $oFeeds = $oService->get('FeedsTable');
            $oFeedData = $oService->get('FeedDataTable');
            
            //$oFeedData->test(); 
            
            
            $feed = $oFeeds->getFeedByID( $iFeedId );     
            $obj = $oFeedData->setServiceLocator($oService)->initialize();
            $oCategory = $oService->get('FeedsCategoryTable');
            $parentNode = 'product';
            $oAttributes = $oService->get('ProductAttributesTable');     
            $plugin = $this->ReadXMLByNodePlugin();    
            $plugin	->setFileName('')->setParentNode($parentNode)->initialize();
            $oAdvertiser = $oService->get("{$feed['className']}Table");
            $oAdvertiser->getReady( $feed['id'] );          

            $sNode = simplexml_load_string( '<product>
<programname>Hudson&#39;s Bay</programname>
<programurl>http://www.thebay.com</programurl>
<catalogname>The Bay Product Catalog (English)</catalogname>
<lastupdated>2016-02-12 06:22:38.164</lastupdated>
<name>Calvin Klein Techcool No Show Liner-BLACK-7-12</name>
<keywords>CALVIN KLEIN Techcool No Show Liner,86849508</keywords>
<description>A soft, Techcool liner which captures moisture and disperses heat from foot, helping to keep feet cool and dry, with mesh panels and reinforced heel/ toe for durability.</description>
<sku>86849508_BLACK_7-12</sku>
<manufacturer>Calvin Klein</manufacturer>
<manufacturerid>86849508</manufacturerid>
<upc>58665481382</upc>
<currency>CAD</currency>
<saleprice>10.00</saleprice>
<price>10.00</price>
<buyurl>http://www.dpbolvw.net/click-7397357-10995993?url=http%3A%2F%2Fwww.thebay.com%2Fwebapp%2Fwcs%2Fstores%2Fservlet%2Fen%2Fthebay%2Ftechcool-no-show-liner-0037-mcv126--24</buyurl>
<impressionurl>http://www.lduhtrp.net/image-7397357-10995993</impressionurl>
<imageurl>http://s7d9.scene7.com/is/image/TheBay/058665481382_main?$PDPLARGE$</imageurl>
<advertisercategory>Men&#39;s &gt; Underwear &amp; Socks &gt; Socks &gt; Dress</advertisercategory>
<thirdpartycategory>BLACK</thirdpartycategory>
<title>7-12</title>
<format>MEN</format>
<promotionaltext>Free Shipping on purchases of $99 or more, plus easy returns</promotionaltext>
<instock>yes</instock>
<condition>new</condition>
<standardshippingcost>5.95</standardshippingcost>
</product>' );
            $aProcessedData = $oAdvertiser->getProcessedData( $sNode );
            echo '<pre>';
            print_r($aProcessedData);
            echo '</pre>';
            die;
            if( is_array($aProcessedData) ) {
                //$oFeedData->insertData( $aProcessedData );
            }            
    }
    
    public function processCJAction()
    {
        $bLiveProcessinge = true; 
        
        $request = $this->getRequest();
        
        if( $bLiveProcessinge && false ) {       
            
            if ( !($request instanceof ConsoleRequest) ){
                throw new \RuntimeException('You can only use this action from a console!');
                exit(0);
            }
        }        
        
        if ($request instanceof ConsoleRequest){
            $iFeedId   = $request->getParam('feed_id', false);
        
        }
        else {
            //throw new \RuntimeException('You can only use this action from a console!');
            $aGetParams = $this->params()->fromQuery();
            $iFeedId = $aGetParams['feed_id'];
        }    

        echo $iFeedId . "Feed ID: {$iFeedId} \n";
        
        if( $bLiveProcessinge ) {
        	@mkdir('/home/CJBackups');
        	@mkdir($backupDir = '/home/CJBackups/'.date('Ymd'));
        }
    	echo "backups done \n";
    	
    	$parentNode = 'product';
    	
    	$oService = $this->getServiceLocator();
    	$oFeeds = $oService->get('FeedsTable');
    	
    	 
    	$oFeedData = $oService->get('FeedDataTable');
    	
    	$feed = $oFeeds->getFeedByID( $iFeedId );
    	
    	$files = glob($feed['fileIdentifier']);
    	if( !count($files) ) {
    	    echo "No file found!";
    	    die;
    	}
    	
    	
    	$obj = $oFeedData->setServiceLocator($oService)->initialize();
    	if( $bLiveProcessinge ) {
    	    echo "truncateTables called \n";
    	   $obj->truncateTables( $iFeedId );
    	}
    	 
    	$oCategory = $oService->get('FeedsCategoryTable');
    	
    	$oAttributes = $oService->get('ProductAttributesTable');
    	$i =1; 
    	
    	
    	
    	//foreach($aFeeds as $feed) 
    	{
    	    
    	    echo "feeds found \n";    		
    		$plugin = $this->ReadXMLByNodePlugin();
    		
    		
    		
    		
    		foreach ( glob($feed['fileIdentifier']) as $file ) {
    			
    		    echo date('Y-m-d H:i:s') . "start processing {$file} \n";
    		    
    			if( $bLiveProcessinge ) copy($file, $backupDir.'/'.basename($file));  
    			if( strstr($file, '.gz') ) exec("gunzip -f {$file}");
    			$file = str_replace('.gz', '', $file);
    			echo date('Y-m-d H:i:s') . "file un-archived \n";
    			
    			if( file_exists($file) ) {
    				
	    			$plugin	->setFileName($file)->setParentNode($parentNode)->initialize();   

	    			$oAdvertiser = $oService->get("{$feed['className']}Table");
	    			$oAdvertiser->getReady( $feed['id'] );
	    			echo date('Y-m-d H:i:s') . "{$feed['className']}Table loaded \n";
	    			//echo "processCJ"; exit(0);
	    			
	    			while( $sNode = $plugin->getNode() ) {
	    				
	    				//echo " {$i}      \r"; $i++;
	    				//echo $sNode . "\n\n\n";
	    				if($sNode=='ERROR') continue;
	    				 
	    				$aProcessedData = $oAdvertiser->getProcessedData( $sNode );
	    				
	    				if( is_array($aProcessedData) ) {
	    					$oFeedData->insertData( $aProcessedData );
	    				}
	    			}
    			 
	    			if( $bLiveProcessinge && false ) {
	    			    @unlink($file);
	    			}
    			}
    		}
    	}
    	
    	$oFeedData->finalize( $iFeedId );
    	
    	/*$oFeedData->assignRandomProcessIds();
    	
    	for($i=1; $i<=10; $i++) {
    		exec("/usr/local/zend/bin/php /var/www/FindYourStitch/public/index.php feed processimages {$i} > /dev/null &"); 
    	}*/
    	
    	
    	exit(0);
    }

    /*public function processImagesAction()
    {
    	$request = $this->getRequest();
    	if ($request instanceof ConsoleRequest){
    		$iProcessId   = $request->getParam('processId', false);
    	
    	}
    	else {
    		//throw new \RuntimeException('You can only use this action from a console!');
    		$aGetParams = $this->params()->fromQuery();
    		$iProcessId = $aGetParams['processId'];
    	}

    	$oService = $this->getServiceLocator();
    	$oFeedData = $oService->get('FeedDataTable');
    	$aFeedImages = $oFeedData->getFeedImagesByProcessId( $iProcessId );
    	
    	foreach ($aFeedImages as $aImage) {
    		
    		$ext = 'unk';
    		
    		$c = curl_init();
    		curl_setopt($c, CURLOPT_URL, $aImage['imageurl'] );
    		curl_setopt($c, CURLOPT_HEADER,1);
    		curl_setopt($c, CURLOPT_NOBODY,1);
    		curl_setopt($c, CURLOPT_RETURNTRANSFER,1);
    		curl_setopt($c, CURLOPT_TIMEOUT,60);
    		$cURL_RESULT = curl_exec($c);
    		if($cURL_RESULT !== FALSE) {
	    		$urlInfo = curl_getinfo($c);
    		}
    		else {
    			$urlInfo = array();
    		}
    		curl_close($c);
    		
    		$oFeedData->updateFeedDataWithImageInfo( $aImage, $urlInfo );
    	}
    	
    	exit(0);
    }*/
    public function addInfoAction() 
    {
    	//$this->layout('layout/empty');
    	$__viewVariables = array();
    	$aGetParams = $this->params()->fromQuery();
    	$aPostParams = $this->params()->fromPost();
    	$viewModel = new ViewModel();
    	$oService = $this->getServiceLocator();
    	//$viewModel->setTerminal(true); // Disable layout.
    	
    	$oAttributes = $oService->get('ProductAttributesTable');    	
    	$__viewVariables['attributes'] = $oAttributes->getAttributesTree();
    	
    	$oFeedData = $oService->get('FeedDataTable');
    	$__viewVariables['item'] = $oFeedData->getItem( $aGetParams['pid'] );
    
    	return $__viewVariables;	
    }
    
    public function listAction()
    {
    	$this->layout()->showSmallHeader = true;
    	$this->layout()->showHeaderLinks = "LOGGED_IN";
    	
    	$__viewVariables = array();
    	$oService = $this->getServiceLocator();
    	$aPostParams = $this->params()->fromPost();
    	 
    	$__viewVariables['form'] =  new \Application\Form\ProductsListFiltersForm( $oService, $aPostParams );
    	
   		$oFeedData = $oService->get('FeedDataTable');    
   		$oAuth = $oService->get('AuthService');
   		 
   		if( isset($aPostParams['ArticlesPerPage']) ) {
   			$oAuth->getStorage()->set('service', array('ArticlesPerPage'=>$aPostParams['ArticlesPerPage']));
   		}
   		
   		$aServiceData = $oAuth->getStorage()->get('service');
   		$__viewVariables['articlesPerPage'] = (isset($aServiceData['ArticlesPerPage']) ? $aServiceData['ArticlesPerPage'] : 50);
   		
   		$paginator = $oFeedData->getFeedData( $aPostParams, true );
   		$paginator->setCurrentPageNumber( (isset($aPostParams['page']) ? $aPostParams['page'] : 1) );
   		$paginator->setItemCountPerPage( $__viewVariables['articlesPerPage'] );
   		
   		//print_r($paginator->getPages());
   		
   		$__viewVariables['feedData'] = $paginator;
   		$__viewVariables['pages'] = $paginator->getPages();
    	
        return $__viewVariables;
    }    
    
    
    
//     public function articlesListAction() {
//         $__viewVariables = array();
        
//         $oHelper = $this->getServiceLocator()->get('viewhelpermanager');
//         $oHelper->get('HeadScript')->appendFile('/public/fancybox/source/jquery.fancybox.js');
//         $oHelper->get('headLink')->appendStylesheet('/public/fancybox/source/jquery.fancybox.css');
//         $oHelper->get('HeadScript')->appendFile('/public/js/jquery.datetimepicker.js');
//         $oHelper->get('headLink')->appendStylesheet('/public/css/jquery.datetimepicker.css');
    
//         $oAuth = $this->getServiceLocator()->get('AuthService');
//         $oService = $this->getServiceLocator();
//         $outfitTable = $oService->get('FeedDataTable');
//         $request = $this->getRequest();
//         $response = new \stdClass();
//         if (!$oAuth->getIdentity()) {
//             return $this->redirect()->toUrl('/service');
//         }
    
//         $this->layout()->showSmallHeader = true;
//         $this->layout()->showHeaderLinks = "NOT_LOGGED_IN";
//         if ($oAuth->hasIdentity()) {
//             $this->layout()->showHeaderLinks = "LOGGED_IN";
//             $userInfo = $oAuth->getIdentity();
//             $userId = $userInfo->userId;
//             $this->layout()->firstName = $userInfo->firstName;
//         } else {
//             $this->redirect()->toRoute('auth');
//         }
    
//         $iPage = 1;
//         $posts = array();
//         if ($request->isPost()) {
//              $posts = $request->getPost();
//              $posts['post'] = 1;
//              $__viewVariables['postData'] = $posts;
             
//              if( isset($posts['page']) && !empty($posts['page']) ) {
//                 $iPage = $posts['page'];
//              }
// //             $searchResult = $outfitTable->getOutfitsProducts(array('user_id' => $oAuth->getIdentity()->userId), $posts['searchkey']);
// //             $response->total = count($searchResult);
// //             $response->result = $searchResult;
//         }
    
//         if ($request->isXmlHttpRequest()) {
//             die(\Zend\Json\Json::encode($response, true));
//         }
    
//         //return new ViewModel(array('outfits' => $outfitTable->getUsersOutfitsProducts(array('user_id' => $oAuth->getIdentity()->userId))));
    
//         // grab the paginator from the AlbumTable
//         $paginator = $outfitTable->getProductsForArticleManagement( $posts );
//         // set the current page to what has been passed in query string, or to 1 if none set
//         $paginator->setCurrentPageNumber($iPage);
//         // set the number of items per page to 10
//         $paginator->setItemCountPerPage(20);
//         $__viewVariables['paginator'] = $paginator;
//         //print_r($paginator); die;
        	
//         return new ViewModel($__viewVariables);
    
//     }    
     public function articlesListAction() {
    
   
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

        $__viewVariables = array();

        $this->layout()->showSmallHeader = true;
        $this->layout()->hideFooter = true;

        $this->layout()->showHeaderLinks = "NOT_LOGGED_IN";
        $oAuth = $this->getServiceLocator()->get('AuthService');
        $userId='';
        if ($oAuth->hasIdentity() && $oAuth->getIdentity()->userType==2) {
            $this->layout()->showHeaderLinks = "LOGGED_IN";
            $userInfo = $oAuth->getIdentity();
            $userId = $userInfo->userId;
            $this->layout()->firstName = $userInfo->firstName;
        } else {
            $this->redirect()->toRoute('auth');
        }

        $oService = $this->getServiceLocator();
        $oAttributes = $oService->get('ProductAttributesTable');
        

        $oFeedData = $oService->get('FeedDataTable');

        $oFeedMapping = $oService->get('FeedsMappingTable');
        $oAuth = $oService->get('AuthService');

   
        $aServiceData = $oAuth->getStorage()->get('service');
        $__viewVariables['articlesPerPage'] = (isset($aServiceData['ArticlesPerPage']) ? $aServiceData['ArticlesPerPage'] : PRODUCTS_PER_PAGE);

        $paginator = $oFeedData->getFeedData(null, true, null, null, null);
        $paginator->setCurrentPageNumber((isset($aGetParams['page']) ? $aGetParams['page'] : 1));
        $paginator->setItemCountPerPage($__viewVariables['articlesPerPage']);        
        $paginatorArr = array();
        foreach ($paginator as  $value) {
            $paginatorArr[] = $value;
        }
        $count = $oFeedData->countFilterFeedData(null, true,$userId,null,true); 
         $__viewVariables['feedCount'] = $count;
 
         /* my code*/
        $AttributesLevel2 = $oAttributes->getAttributesLevel2();
        foreach ($paginatorArr as &$value) {
            if($value->productInfoAdded == 'yes') {
                //$listProductAttr = $oAttributes->getAttributes($value->uid)['categories'];
                $listProductAttr =  $oAttributes->getAttributesNoTree($value->uid);
                $listProductAttr_id = array();
                $parrent_id = null;
                foreach ($listProductAttr as $lpa) {
                    if($parrent_id != $lpa['attributeParentId']) {
                        $listProductAttr_id[] =  $lpa['attributeParentId'];
                        $parrent_id = $lpa['attributeParentId'];
                    }
                    
                }
                   // echo '<pre style="color:blue">';
                   //                  var_dump($listProductAttr_id);
                   //                  echo '</pre>';    
                   //                  die(__FILE__);
          
                $break = false;
                foreach ($listProductAttr as $val) {
                    if($break  == true) break;
                    if($val['type'] == 'categories') {
                        foreach ($AttributesLevel2 as $attrlv2) {
                            if($val['attributeId'] == $attrlv2['attributeId']){
                                $idlv2 = $val['attributeId'];
                                $AttributesLevel3 = $oAttributes->getAttributesLevel3($idlv2);
                                $AttributesLevel3_id = array();
                                foreach ($AttributesLevel3 as $attlv3) {
                                    $AttributesLevel3_id[] = $attlv3['attributeId'];
                                }

                                if($AttributesLevel3_id ==  array_intersect($AttributesLevel3_id, $listProductAttr_id)) {
                                     $value->icon = 'green';
                                }
                                else {
                                    $value->icon = 'orange';
                                }

                                $break == true;
                                break;
                            }
                        }
                    }

                }
            } else {
                $value->icon = 'red';
            }

        }
        /* end my code*/

        $paginator->setCurrentPageNumber((isset($aGetParams['page']) ? $aGetParams['page'] : 1));
        $paginator->setItemCountPerPage($__viewVariables['articlesPerPage']);
        //print_r($paginator->getPages());

      
        $__viewVariables['attributes'] = $oAttributes->getAttributesTree(1);

        $__viewVariables['feedData'] = $paginatorArr;
        $__viewVariables['pages'] = $paginator->getPages();

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

        $maxRangePrice = $oFeedData->getMaxRangePrice();
        $__viewVariables['maxRangePrice'] = $maxRangePrice;
        $__viewVariables['allColors'] = $allColors; 

        return $__viewVariables;
    }
    
  

    public function printAttributes($arr){

        foreach($arr as $att ) {
             if(count($att[])) {
                printAttributes( $att['child'], $level, $att['id'], $product_attributes );
            }
        }
    }

    public function editArticleAction() {
        if ($this->userId) {
            $this->layout()->showHeaderLinks = "LOGGED_IN";
        }
        $this->layout()->hideFooter = true;
        
        //$this->layout('layout/empty');
        $__viewVariables = array();
        $aGetParams = $this->params()->fromQuery();
        $aPostParams = $this->params()->fromPost();
    
        $oService = $this->getServiceLocator();
        //$viewModel->setTerminal(true); // Disable layout.
         
        $oAttributes = $oService->get('ProductAttributesTable');
        $__viewVariables['attributes'] = $oAttributes->getAttributesTree(0, false, '3D');
        $__viewVariables['level1CategoryIds'] = $oAttributes->getLevel1CategoryIds('3D');
        $__viewVariables['level2CategoryIds'] = $oAttributes->getLevel2CategoryIds('3D');
        $__viewVariables['categoryTypeIds'] = $oAttributes->getCategoryTypeIds();
        //$arr = $oAttributes->getAttributeParentId();

        $oFeedData = $oService->get('FeedDataTable');
        $__viewVariables['item'] = $oFeedData->getItem( $aGetParams['pid'] );
        //print_r($__viewVariables['item']); die;
      
        $oProductArrtibutes = $oService->get('ProductAttributesTable');
        $__viewVariables['product_attributes'] = array_filter($oProductArrtibutes->getAttributes($__viewVariables['item']['uid'], 'title'));    

        $__viewVariables['product_attributes_ids'] = array();
        foreach($__viewVariables['product_attributes'] as $catLevel1 ) {
            
            foreach($catLevel1 as $catLevel2 ) {
                $__viewVariables['product_attributes_ids'][] = $catLevel2['attributeId'];
            }            
        }
      
        //$oFeedMapping = $oService->get('FeedsMappingTable');

       //$__viewVariables['allColors'] = $oFeedMapping->getDistinctColor();

        
        if($this->getRequest()->isPost()){
        
           $data = array();
           $productUID = $__viewVariables['item']['uid'];

            $data = array();
            $aPostParams = $this->params()->fromPost();
            if(isset($aPostParams['categories']) &&!empty($aPostParams['categories'])){
                
                $oAttributes ->deleteAllProductAttributes($productUID);
                
                $dataPost = json_decode($aPostParams['categories']);
                foreach ($dataPost as $value) {
                    $aParts = explode(':', $value);
                    if( $aParts[0] == 'catagory' || $aParts[0] == 'attribute' ) {
                        $attribParts = explode(',', $aParts[1]);
                        foreach($attribParts as $val) {
                            $aRefAtrribs = $oAttributes->addProductAttributes_Categories($productUID, $val);
                        }
                    }
                }
                
                $oAuth = $this->getServiceLocator()->get('AuthService');
                $userInfo = $oAuth->getStorage()->read();
                $dataFeed = array(
                    'productInfoAddedBy' => $userInfo->userId,
                    'productInfoAddDate' => date('Y-m-d H:i:s'),
                    'productInfoAdded' => 'yes',
                );
                $oFeedData->updateData($dataFeed,$__viewVariables['item']['id']);                
                
            }
//             if(isset($_POST['categories']) && count($_POST['categories']) > 0){
                
//                  $dataPost = array_filter(explode(',', $_POST['categories'])) ;

//                 foreach ($dataPost as $value) {
//                     $item[] = $oAttributes->getAttrbyCondition($value);
//                 }

//                 foreach ($item as $value) {
//                     //if(trim($value['type'])!='') 
//                     $data[$value['type']][] = array('value'=>$value['value'], 'attributeParentId' => $value['attributeParentId'], 'attributeId' => $value['attributeId']);
//                 }
                
//                  $oAttributes ->deleteAllProductAttributes($productUID);

//             }




//             if(count( $data) > 0) {
//                 $oAttributes->addProductAttributes($productUID,$data);


//                 $oAuth = $this->getServiceLocator()->get('AuthService');
//                 $userInfo = $oAuth->getStorage()->read();


//                // $date = new \DateTime();
//                 $dataFeed = array(
//                     'productInfoAddedBy' => $userInfo->userId,
//                     'productInfoAddDate' => date('Y-m-d H:i:s'),
//                     'productInfoAdded' => 'yes',
//                 );
//                 $oFeedData->updateData($dataFeed,$__viewVariables['item']['id']);
//             }   

            return $this->redirect()->toUrl("/feed/edit-article?pid={$__viewVariables['item']['id']}");    
        }
        return $__viewVariables;
        
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

            // $oArticleCloseset = $oService->get('ArticleClosesetTable');
            // $likedarticles = $oArticleCloseset->getLikedArticles($userId);
            // $likearray = array();
            // if(count($likedarticles)>0) {
            //     foreach($likedarticles as $likedarticle) {
            //         $likearray[] = $likedarticle['feeddataid'];
            //     }
            // }
            // $__viewVariables['likearray'] = $likearray;

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
            $paginator = $oFeedData->getFilterFeedData($aPostParams, true,null,null,null);
            $count = $oFeedData->countFilterFeedData($aPostParams, true,null,null,null);
            /*my code*/

            

            $oAttributes = $oService->get('ProductAttributesTable');
            $AttributesLevel2 = $oAttributes->getAttributesLevel2();

            foreach ($paginator as &$value) {
            if($value['productInfoAdded'] == 'yes') {
                //$listProductAttr = $oAttributes->getAttributes($value->uid)['categories'];
                $listProductAttr =  $oAttributes->getAttributesNoTree($value['uid']);
                $listProductAttr_id = array();
                $parrent_id = null;
                foreach ($listProductAttr as $lpa) {
                    if($parrent_id != $lpa['attributeParentId']) {
                        $listProductAttr_id[] =  $lpa['attributeParentId'];
                        $parrent_id = $lpa['attributeParentId'];
                    }
                }
                $break = false;
                foreach ($listProductAttr as $val) {
                    if($break  == true) break;
                    if($val['type'] == 'categories') {
                        foreach ($AttributesLevel2 as $attrlv2) {
                            if($val['attributeId'] == $attrlv2['attributeId']){
                                $idlv2 = $val['attributeId'];
                                $AttributesLevel3 = $oAttributes->getAttributesLevel3($idlv2);
                                $AttributesLevel3_id = array();
                                foreach ($AttributesLevel3 as $attlv3) {
                                    $AttributesLevel3_id[] = $attlv3['attributeId'];
                                }

                                if($AttributesLevel3_id ==  array_intersect($AttributesLevel3_id, $listProductAttr_id)) {
                                     $value['icon'] = 'green';
                                }
                                else {
                                    $value['icon'] = 'orange';
                                }

                                $break == true;
                                break;
                            }
                        }
                    }

                }
            } else {
                $value['icon'] = 'red';
            }

        }
        /* end my code*/

            $oAttributes = $oService->get('ProductAttributesTable');
            $__viewVariables['attributes'] = $oAttributes->getAttributesTree(1);

            $__viewVariables['feedData'] = $paginator;
            $__viewVariables['feedCount'] = $count;
            $viewModel = new ViewModel($__viewVariables);
            $viewModel->setTerminal(true);
            return $viewModel;
            // return $this->response;
        }
    }
    
    public function generateOutfitsPreviewImageAction()
    {
        $request = $this->getRequest();
        if (!($request instanceof ConsoleRequest)){
           echo "error: web request not allowed.\n";
           die;
    
        }
        $oService = $this->getServiceLocator();
        $oOutfitsProducts = $oService->get('OutfitsProductsTable');
        $oOutfitsFeedsMapping = $oService->get('OutfitsFeedsMappingTable');
        $aOutfits = $oOutfitsProducts->getAllOutfits();
        foreach($aOutfits as $aOutfit) {
            echo $aOutfit['outfit_id'] . "\n";
            $oOutfitsFeedsMapping->generateOutfitImage( $aOutfit['outfit_id'] );
        }

        echo "done \n";
        exit(0);
    }    
    
    public function deleteUnUsedAttributesAction()
    {
        $request = $this->getRequest();
        if (!($request instanceof ConsoleRequest)){
            echo "error: web request not allowed.\n";
            die;
    
        }
        $oService = $this->getServiceLocator();
        $oProductAttributes = $oService->get('ProductAttributesTable');
        $oProductAttributes->deleteUnUsedAttributes();

        echo "done \n";
        exit(0);
    }  
    
    public function updateCatagoriesTableAction()
    {
        $request = $this->getRequest();
        if (!($request instanceof ConsoleRequest)){
            echo "error: web request not allowed.\n";
            die;
    
        }
        $oService = $this->getServiceLocator();
        $oProductCategories = $oService->get('ProductCategoriesTable');
        $oProductCategories->updateCatagoriesTable();

        echo "done \n";
        exit(0);
    }  
    
    public function globalFinalizeAction()
    {
        $request = $this->getRequest();
        if (!($request instanceof ConsoleRequest)){
            echo "error: web request not allowed.\n";
            die;
    
        }
        $oService = $this->getServiceLocator();
        $oFeedData = $oService->get('FeedDataTable');
        $oFeedData->globalFinalize();
    
        echo "done \n";
        exit(0);
    }    

    public function processBucketProductsAction()
    {
//         $request = $this->getRequest();
//         if (!($request instanceof ConsoleRequest)){
//             echo "error: web request not allowed.\n";
//             die;
        
//         }
        $oService = $this->getServiceLocator();
        $oOutfitsProducts = $oService->get('BucketDefinitionTable');    
        $oOutfitsProducts->addProductsToBuckets();
        
    }
}
