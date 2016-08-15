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
use Application\Model\FeedDataTable;
use Zend\Console\Request as ConsoleRequest;

class FeedController extends AbstractActionController
{
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
    
    public function processCJAction()
    {
    	/*$request = $this->getRequest(); 
    	if ( !($request instanceof ConsoleRequest) ){
    		throw new \RuntimeException('You can only use this action from a console!');
    	}*/
    	//exit(0);
    	@mkdir('/home/CJBackups');
    	@mkdir($backupDir = '/home/CJBackups/'.date('Ymd'));
    	
    	echo "backups done \n";
    	
    	$parentNode = 'product';
    	
    	$oService = $this->getServiceLocator();
    	$oFeeds = $oService->get('FeedsTable');
    	$aFeeds = $oFeeds->getFeeds();
    	 
    	$oFeedData = $oService->get('FeedDataTable');
    	$oFeedData->setServiceLocator($oService)->initialize()->truncateTables();
    	 
    	$oCategory = $oService->get('FeedsCategoryTable');
    	
    	$oAttributes = $oService->get('ProductAttributesTable');
    	$i =1; 
    	foreach($aFeeds as $feed) {
    	    
    	    echo "feeds found \n";    		
    		$plugin = $this->ReadXMLByNodePlugin();
    		
    		print_r(glob($feed['fileIdentifier']));
    		
    		
    		foreach ( glob($feed['fileIdentifier']) as $file ) {
    			
    		    echo "start processing {$file} \n";
    		    
    			copy($file, $backupDir.'/'.basename($file)); 
    			if( strstr($file, '.gz') ) exec("gunzip -f {$file}");
    			$file = str_replace('.gz', '', $file);
    			echo "file un-archived \n";
    			
    			if( file_exists($file) ) {
    				
	    			$plugin	->setFileName($file)->setParentNode($parentNode)->initialize();   

	    			$oAdvertiser = $oService->get("{$feed['className']}Table");
	    			$oAdvertiser->getReady( $feed['id'] );
	    			echo "{$feed['className']}Table loaded \n";
	    			//echo "processCJ"; exit(0);
	    			
	    			while( $sNode = $plugin->getNode() ) {
	    				
	    				echo " {$i}      \r"; $i++;	    
	    				//echo $sNode . "\n\n\n";
	    				if($sNode=='ERROR') continue;
	    				 
	    				$aProcessedData = $oAdvertiser->getProcessedData( $sNode );
	    				
	    				if( is_array($aProcessedData) ) {
	    					$oFeedData->insertData( $aProcessedData );
	    				}
	    			}
    			 
	    			@unlink($file);
    			}
    		}
    	}
    	
    	$oFeedData->finalize();
    	
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
}
