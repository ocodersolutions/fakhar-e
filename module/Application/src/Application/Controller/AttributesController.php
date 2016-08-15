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

class AttributesController extends AbstractActionController
{
    public function indexAction()
    {
 		echo "Test"; exit();
    }
    
    public function getCategoriesJsonAction()
    {
    	$aGetParams = $this->params()->fromQuery();
    	
    	$oService = $this->getServiceLocator();
   		$oAttributes = $oService->get('ProductAttributesTable');
   		if( isset($aGetParams['mapped']) && $aGetParams['mapped']=='true' ) $sGetAttributesTreeFunction = 'getAttributesTreeForUIMapped';
   		else $sGetAttributesTreeFunction = 'getAttributesTreeForUI';
   		$aAttributes = $oAttributes->{$sGetAttributesTreeFunction}( isset($aGetParams['id']) && !empty($aGetParams['id']) ? $aGetParams['id'] : 1 );
   		//print_r($aAttributes); exit(0);
   		$iLevel = isset($aGetParams['levels']) && !empty($aGetParams['levels']) ? $aGetParams['levels'] : 4;
   		$aExcludeCats = isset($aGetParams['ec']) && !empty($aGetParams['ec']) ? explode(',', $aGetParams['ec']) : array();
   		$aJson = $this->printAttributes( $aAttributes, $iLevel, $aExcludeCats );
   		//print_r($aJson);
   		echo json_encode($aJson);
   		exit(0);  
																																							
    } 
     
    private function printAttributes($aAttributes, $iLevel, $aExcludeCats)
    {
       	$aFinal = array();
   		foreach( $aAttributes as $attLevel1 ) {
//    			if($attLevel1['depth'] > $iLevel) {
//    				return array();
//    			}
    			$aTemp = array();   				
   			$aTemp['id'] = $attLevel1['id'];
   			$aTemp['label'] = $attLevel1['title'];
   			$aTemp['inode'] = (isset($attLevel1['child']) && is_array($attLevel1['child']) && count($attLevel1['child']));
   			$aTemp['open'] = false;
   			$aTemp['checkbox'] = true;
   			$aTemp['radio'] = false;
   			$aTemp['checked'] = false;
   			if( in_array($attLevel1['id'], $aExcludeCats) ) {
   				$aTemp['checked'] = false;
   			}
   			
   			if(@count($attLevel1['child'])) {
   				$aTemp['branch'] = $this-> printAttributes( $attLevel1['child'], $iLevel, $aExcludeCats );
   				
   				foreach( $aTemp['branch'] as $val ){
   					if( $val['checked']===false ) {
   						$aTemp['open'] = false;
   						break;
   					}
                                        
   				}
   			}
   			
   			$aFinal[] = $aTemp;
   		}  
   		return $aFinal;	
    }
}

