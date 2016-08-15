<?php

namespace Application\Model;

use Application\Model\AdvertiserTable;

class AdvertiserBoscovTable extends AdvertiserTable 
{
	public function getProcessedData( $oNode )
	{
		$aData = array();
		
		$this->basicData( $oNode, $aData );
		$this->addProductUID( $aData );
		if( !(isset($aData['basicData']['upc']) && strlen($aData['basicData']['upc'])) ) {
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
	
	protected function isValidCategory( $col1 )
	{
	    return (
	        ( 
	            stristr($col1, 'Men>') ||
	            stristr($col1, '>Men')
	        ) &&
	        !stristr($col1, 'Women') &&
	        !stristr($col1, 'Ladies')
	    );
	}	

	protected function addProductUID( &$aData )
	{
	    $aData['basicData']['uid'] = $this->iFeedId . '_' . md5($aData['basicData']['description'] . '_' . $aData['basicData']['imageurl']);
	}
		
	private function addAdditionalInfo( &$aData )
	{
		$aData['basicData']['brand'] = $this->extractBrand( $aData['basicData']['caption'] . ' ' . $aData['basicData']['name'] . ' ' . $aData['basicData']['keywords'] . ' ' . $aData['basicData']['description'] );
		
		$aData['basicData']['caption'] = str_replace($aData['basicData']['brand'], '', $aData['basicData']['name']);
		$aData['basicData']['caption'] = trim(substr($aData['basicData']['caption'], 0, strpos($aData['basicData']['caption'], '-')));
		
		$aData['basicData']['imageurl'] = str_replace(array('/product/thumbnails/', 't.jpg'), array('/product/images/', '.jpg'), $aData['basicData']['imageurl']);
		
		
		
		
		//$aData['attributes']['color'] = $this->extractColor( $aData['basicData']['caption'] . ' ' . $aData['basicData']['name'] . ' ' . $aData['basicData']['keywords'] . ' ' . $aData['basicData']['description'] );
				
// 		$aData['attributes']['image'] = $aData['basicData']['imageurl'];
		
// 		$aValues = false;
// 		if( in_array('6', $aData['attributes']['categories']) ) { // Shirts
// 			$aData['attributes']['size'] = $this->extractShirtSize( $aData['basicData']['name'] );
// 		}
// 		else if( in_array('7', $aData['attributes']['categories']) ) { // Pants
// 			$aData['attributes']['size'] = $this->extractPantSize( $aData['basicData']['name'] );		
// 		}	
// 		else if( in_array('4', $aData['attributes']['categories']) ) { // Shoe
// 			$aData['attributes']['size'] = $this->extractShoeSize( $aData['basicData']['name'] );		
// 		}		
	}
		
}