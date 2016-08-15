<?php

namespace Application\Model;

use Application\Model\AdvertiserTable;

class AdvertiserSSenseTable extends AdvertiserTable 
{
    Public $aAttributeLookupCol = array(
        'type'          => array('advertisercategory', 'keywords'),
        'categories'    => array('advertisercategory', 'keywords'),
        'Color'         => array('thirdpartycategory'),
        'Brand'         => array('keywords'),
        'Default'       => array('caption', 'name', 'keywords', 'description', 'advertisercategory')
    );
    
	public function getProcessedData( $oNode )
	{
		$aData = array();
		
		$this->basicData( $oNode, $aData );
		$this->addProductUID( $aData );
		if( !(isset($aData['basicData']['uid']) ) ) {
			return false;
		}
		
		$aAttributes = $this->getAttributes( $aData['basicData']['advertisercategory'], $aData['basicData']['caption'] . ' ' . $aData['basicData']['name'] . ' ' . $aData['basicData']['keywords'] . ' ' . $aData['basicData']['thirdpartycategory'] );
		if( is_array($aAttributes) ) {
		    $aData = array_merge($aData, $aAttributes);
		    $this->addAdditionalInfo( $aData );
		    return $aData;
		}
		else {
		    return false;
		}		
		
	}

	protected function addProductUID( &$aData )
	{
		//$aData['basicData']['uid'] = $this->iFeedId . '_' . $aData['basicData']['sku'];
		$aData['basicData']['uid'] = $this->iFeedId . '_' . md5( substr($aData['basicData']['buyurl'],strpos($aData['basicData']['buyurl'], '?url')).'_'.$aData['basicData']['imageurl'] );
	}
	
	private function addAdditionalInfo( &$aData )
	{
		$aData['basicData']['brand'] = $this->extractBrand( $aData['basicData']['caption'] . ' ' . $aData['basicData']['name'] . ' ' . $aData['basicData']['keywords'] . ' ' . $aData['basicData']['description'] );
		
		$aData['basicData']['caption'] = str_replace($aData['basicData']['brand'], '', $aData['basicData']['name']);
		$aData['basicData']['caption'] = trim(substr($aData['basicData']['caption'], 0, strpos($aData['basicData']['caption'], '-')));
		
// 		$aData['attributes']['color'] = $this->extractColor( $aData['basicData']['caption'] . ' ' . $aData['basicData']['name'] . ' ' . $aData['basicData']['keywords'] . ' ' . $aData['basicData']['description'] );
				
// 		$aData['attributes']['image'] = $aData['basicData']['imageurl'];
		
// 		$aValues = false;
// 		if( in_array('6', $aData['attributes']['categories']) ) { // Shirts
// 			$aData['attributes']['size'] = $this->extractShirtSize( $aData['basicData']['title'] );
// 		}
// 		else if( in_array('7', $aData['attributes']['categories']) ) { // Pants
// 			$aData['attributes']['size'] = $this->extractPantSize( $aData['basicData']['title'] );		
// 		}	
// 		else if( in_array('4', $aData['attributes']['categories']) ) { // Shoe
// 			$aData['attributes']['size'] = $this->extractShoeSize( $aData['basicData']['title'] );		
// 		}
	
		
	}
		
}