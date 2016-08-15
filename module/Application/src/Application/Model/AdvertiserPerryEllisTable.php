<?php

namespace Application\Model;

use Application\Model\AdvertiserTable;

class AdvertiserPerryEllisTable extends AdvertiserTable 
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
	        //echo "upc fail \n";
	        return false;
	    }
	
	    //$aAttributes = $this->getAttributes( $aData['basicData']['advertisercategory'], $aData['basicData']['caption'] . ' ' . $aData['basicData']['name'] . ' ' . $aData['basicData']['keywords'] . ' ' . $aData['basicData']['thirdpartycategory'] );
	    $aAttributes = $this->getAttributes( $aData['basicData'] );
	    if( is_array($aAttributes) ) {
	        $aData = array_merge($aData, $aAttributes);
	        $this->addAdditionalInfo( $aData );
	        //echo "OK: {$aData['basicData']['upc']} \n";
	        return $aData;
	    }
	    else {
	        //echo "catagory fail: {$aData['basicData']['advertisercategory']} \n";
	        return false;
	    }
	
	}
		
	protected function addProductUID( &$aData )
	{
		$aData['basicData']['uid'] = $this->iFeedId . '_' . md5( substr($aData['basicData']['buyurl'],strpos($aData['basicData']['buyurl'], '?url')).'_'.$aData['basicData']['imageurl'] );
	}
	
	private function addAdditionalInfo( &$aData )
	{
		$aData['basicData']['brand'] = $this->extractBrand( $aData['basicData']['caption'] . ' ' . $aData['basicData']['name'] . ' ' . $aData['basicData']['keywords'] . ' ' . $aData['basicData']['description'] );
		
		$aData['basicData']['caption'] = str_replace($aData['basicData']['brand'], '', $aData['basicData']['name']);
		$aData['basicData']['caption'] = trim(substr($aData['basicData']['caption'], 0, strpos($aData['basicData']['caption'], '-')));
		
	}
		
}