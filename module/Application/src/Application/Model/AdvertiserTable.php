<?php

namespace Application\Model;

//use Application\Model\FeedDataTable;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Expression;

class AdvertiserTable extends BasicTableAdapter 
{
	private $aMapping = array();
	private $aAdditinalMapping = array();
	protected $iFeedId = null;
	
	public function getReady( $iFeedId )
	{
		$this->iFeedId = $iFeedId;
		$this->loadMapping();
	}		
	
	protected function loadMapping()
	{
		$sql = new Sql($this->getDBAdapter());
		
		$sQuery = "
                select * from ( 
		    
            		SELECT l4.title_search 'title', l4.type, l4.value, l4.attributeId, l4.attributeParentId3D 'attributeParentId', l4.depth
            		FROM ProductReferenceAttributes l4
            		INNER JOIN ProductReferenceAttributes l3 on l4.attributeParentId3D = l3.attributeId and l3.isActive3D = 'yes'
            		INNER JOIN ProductReferenceAttributes l2 on l3.attributeParentId3D = l2.attributeId and l2.isActive3D = 'yes'
            		INNER JOIN ProductReferenceAttributes l1 on l2.attributeParentId3D = l1.attributeId and l1.isActive3D = 'yes'
            		where l4.attributeParentId3D in( SELECT attributeId FROM ProductReferenceAttributes a where a.title in ('Type')) and l4.isActive3D = 'yes'
                    
                    union all
                    
             		SELECT l4.title_search 'title', l3.type, l3.value, l3.attributeId, l3.attributeParentId3D 'attributeParentId', l3.depth
            		FROM ProductReferenceAttributes l4
            		INNER JOIN ProductReferenceAttributes l3 on l4.attributeParentId3D = l3.attributeId and l3.isActive3D = 'yes'
            		INNER JOIN ProductReferenceAttributes l2 on l3.attributeParentId3D = l2.attributeId and l2.isActive3D = 'yes'
            		INNER JOIN ProductReferenceAttributes l1 on l2.attributeParentId3D = l1.attributeId and l1.isActive3D = 'yes'
            		where l4.attributeParentId3D in( SELECT attributeId FROM ProductReferenceAttributes a where a.title in ('Type')) and l4.isActive3D = 'yes'   
                    
                    union all
                    
             		SELECT l4.title_search 'title', l2.type, l2.value, l2.attributeId, l2.attributeParentId3D 'attributeParentId', l2.depth
            		FROM ProductReferenceAttributes l4
            		INNER JOIN ProductReferenceAttributes l3 on l4.attributeParentId3D = l3.attributeId and l3.isActive3D = 'yes'
            		INNER JOIN ProductReferenceAttributes l2 on l3.attributeParentId3D = l2.attributeId and l2.isActive3D = 'yes'
            		INNER JOIN ProductReferenceAttributes l1 on l2.attributeParentId3D = l1.attributeId and l1.isActive3D = 'yes'
            		where l4.attributeParentId3D in( SELECT attributeId FROM ProductReferenceAttributes a where a.title in ('Type')) and l4.isActive3D = 'yes'        
                    
                    union all
                    
             		SELECT l4.title_search 'title', l1.type, l1.value, l1.attributeId, l1.attributeParentId3D 'attributeParentId', l1.depth
            		FROM ProductReferenceAttributes l4
            		INNER JOIN ProductReferenceAttributes l3 on l4.attributeParentId3D = l3.attributeId and l3.isActive3D = 'yes'
            		INNER JOIN ProductReferenceAttributes l2 on l3.attributeParentId3D = l2.attributeId and l2.isActive3D = 'yes'
            		INNER JOIN ProductReferenceAttributes l1 on l2.attributeParentId3D = l1.attributeId and l1.isActive3D = 'yes'
            		where l4.attributeParentId3D in( SELECT attributeId FROM ProductReferenceAttributes a where a.title in ('Type')) and l4.isActive3D = 'yes'  
                                
                	union all
                    
                	SELECT l4.title_search, l4.type, l4.value, '', '', ''
                	FROM ProductReferenceAttributes l4
                	where l4.attributeParentId in( SELECT attributeId FROM ProductReferenceAttributes a where a.title not in ('Type', 'Color')) and depth = 4 and l4.isActive = 'yes'
                    
                    union all
                    
                	SELECT title, 'Color', value, '', '', '' FROM FeedsMapping where mappingType = 'color' 
	    
                ) t 
                group by title, type, value		 
		        order by type, length(title) desc 
		    ";
		
        $statement = $this->getDBAdapter()->query($sQuery); 
		$oMapping = $statement->execute();		
		foreach($oMapping as $vMap) {
			$this->aMapping['attributes'][ $vMap['type'] ][ $vMap['title'] ][] = array( 'value'=>$vMap['value'], 'attributeId'=>$vMap['attributeId'], 'attributeParentId'=>$vMap['attributeParentId'], 'depth'=>$vMap['depth'] );
		}
		
		$select = new Select();
		$select->from(array('t' => 'FeedsMapping'))->where("mappingType = 'brand'")->order(new Expression('length(title) desc'));
		$oMapping = $sql->prepareStatementForSqlObject($select)->execute();
		foreach($oMapping as $vMap) {
			$this->aMapping['brand'][] = $vMap['title'];
		}
		
		
		$sQuery = "
            SELECT l4.title, l4.type, l4.value, l4.attributeId, l4.attributeParentId, l3.attributeParentId 'attributeParenParentId'
            FROM ProductReferenceAttributes l4
            INNER JOIN ProductReferenceAttributes l3 on l4.attributeParentId = l3.attributeId
            where l4.attributeParentId in( SELECT attributeId FROM ProductReferenceAttributes a where a.title not in ('Type', 'categories')) and l4.depth = 4
		    ";
		
		$statement = $this->getDBAdapter()->query($sQuery);
		$oMapping = $statement->execute(); 
		foreach($oMapping as $vMap) {
		    $this->aAdditinalMapping[ $vMap['type'] ][ $vMap['attributeParenParentId'] ][ $vMap['value'] ] = array('attributeId'=>$vMap['attributeId'], 'attributeParentId'=>$vMap['attributeParentId']);
		}		
		
	}
	
	protected function basicData( $oNode, &$aData )
	{
		
		//$aParsedURL = parse_url((string)$aData->imageurl);
		//$aPathInfo = pathinfo(basename($aParsedURL["path"]));
		
		$aData['basicData'] = array(
				'feedId' 				=> $this->iFeedId,
				'brand' 				=> '',
				'caption' 				=> '',
				'color' 				=> '',
				'name' 					=> (string)$oNode->name,
				'keywords' 				=> (string)$oNode->keywords,
				'description' 			=> (string)$oNode->description,
				'sku' 					=> (string)$oNode->sku,
				'currency' 				=> (string)$oNode->currency,
				'price' 				=> (string)$oNode->price,
				'buyurl' 				=> (string)$oNode->buyurl,
				'impressionurl' 		=> (string)$oNode->impressionurl,
				'imageurl' 				=> (string)$oNode->imageurl,
				'advertisercategory' 	=> (string)$oNode->advertisercategory,
				'instock' 				=> (string)$oNode->instock,
				'condition'				=> (string)$oNode->condition,
				'format'				=> (string)$oNode->format,
				'manufacturerid' 		=> (string)$oNode->manufacturerid,
				'promotionaltext' 		=> (string)$oNode->promotionaltext,
				'retailprice' 			=> (string)$oNode->retailprice,
				'saleprice' 			=> (string)$oNode->saleprice,
				'standardshippingcost' 	=> (string)$oNode->standardshippingcost,
				'thirdpartycategory' 	=> (string)$oNode->thirdpartycategory,
				'title' 				=> (string)$oNode->title,
				'upc' 					=> (string)$oNode->upc,
		        'item_count'            => 1,
		        'item_count_temp'       => 1,
				'img_program_dir'		=> preg_replace("/[^a-zA-Z0-9]/", "", ((string)$oNode->programname) )/*,
				
				'img_local_name' 		=> md5( (string)$oNode->imageurl ) . '.' . $aPathInfo['extension']*/
		);		
	}
	
	protected function isValidCategory( $col1 )
	{
		return ( 
				(stristr($col1, 'Men') || stristr($col1, 'Male')) && 
				!stristr($col1, 'Women') &&
				!stristr($col1, 'Ladies') &&
		        !stristr($col1, 'Female')
		);
	}	

	protected function addProductUID( &$aData )
	{
		//$aData['basicData']['uid'] = $this->iFeedId . '_' . $aData['basicData']['upc'];
		$buyurl = preg_replace('/http:\/\/(.*?)\//', '', $aData['basicData']['buyurl']);
		$aData['basicData']['uid'] = $this->iFeedId . '_' . md5($buyurl);		
	}
	
	//public function getAttributes( $advertisercategory, $sRawData )
	public function getAttributes( $aRawData )
	{
	    if( !$this->isValidCategory( isset($this->aAttributeLookupCol['ValidCategory']) ? $aRawData[$this->aAttributeLookupCol['ValidCategory']] : $aRawData['advertisercategory'] ) ) {
	        return false;
	    }	 

	    //$sRawData = $sRawData . ' ' . $aData['basicData']['advertisercategory'];  
	    
	    $aData = array();
	    $iLevel2Parent = 0;
	    foreach($this->aMapping as $aTypeKey => $aTypeValue) {
	        if( $aTypeKey == 'brand' ) {
	            continue;
	        }
	        foreach($aTypeValue as $aAttributeKey => $aAttributeValue ) {
	            
// 	            if( $aAttributeKey == 'Brand' ) {
// 	                continue;
// 	            }	            
	            
	            if( $aAttributeKey == 'categories' ) {
	                $a = '';
	            }
	            
	           $sRawData = '';
	           $aColArr = isset($this->aAttributeLookupCol[$aAttributeKey]) ? $this->aAttributeLookupCol[$aAttributeKey] : $this->aAttributeLookupCol['Default']; 
	           foreach( $aColArr as $col) {
	               $sRawData .= $aRawData[$col] . ' ';
	           }

	            	            
	            foreach($aAttributeValue as $key => $valArray ) {
	                
	                // Already have find a catagory so ignore others
	                if( $iLevel2Parent > 0 && $aAttributeKey == 'categories' ) {
	                    break;
	                }
	                	                
	                foreach($valArray as $val ) {
     
	                    if( strstr($key, 'Underwear') ) {
	                        $a = '';
	                    }
	                    	                    
                        //if( $this->contains_all(array('rawData'=>$sRawData, 'advCat'=>$aData['basicData']['advertisercategory']), $key, $aAttributeKey) ) {
                        if( $this->contains_all($sRawData, $key, $aAttributeKey) ) {
                            
                            if( $aAttributeKey=='categories' && $val['depth']==2 ) {
                                $iLevel2Parent = $val['attributeParentId'];
                            }
                                                 
                            $aData[$aTypeKey][$aAttributeKey][] = $val;
                            if( !in_array($aAttributeKey, array('categories', 'Feature', 'Season', 'Wash', 'Washing Instructions')) ) {
                                break 2;
                            }                       
                        }
	                }
	            }
	        }
	    }
        
        if( @is_array($aData['attributes']['categories']) && @count($aData['attributes']['categories']) ) {   
                 
            if( $iLevel2Parent > 0 ) {
                foreach($aData['attributes'] as $key => &$values) {
                    foreach($values as &$val) {
                        if( isset($this->aAdditinalMapping[$key][$iLevel2Parent][$val['value']]) ) {
                            $val['attributeId'] = $this->aAdditinalMapping[$key][$iLevel2Parent][$val['value']]['attributeId'];
                            $val['attributeParentId'] = $this->aAdditinalMapping[$key][$iLevel2Parent][$val['value']]['attributeParentId'];
                        }
                    }
                }
            }
        
			return $aData;
		}
		else {
			return array(); 
		}

	}
	
	protected function contains_all($aData, $words, $type) {
	    
	    // Yes / No fields $sRawData
	    if( in_array($type, array('Belt Loop', 'Darts', 'Hood', 'waterproof')) ) {
	        return (stripos($aData, $type) === true);
	    }
	    
	    preg_match_all('/!(.*?)!/', $words, $remove);
	    if( isset($remove[1]) && is_array($remove[1]) && count($remove[1]) ) {
	       foreach($remove[1] as $val) {
	           $aData = str_replace($val, '', $aData);
	           $words = str_replace('!'.$val.'!', '', $words);
	       }
	    }

	    //$words = explode(' ', $words);
	    preg_match_all('/\[.*?\]|[^ ]+/', $words, $words);
	    array_walk_recursive($words, function(&$v){$v= trim($v,'[]');} );
	    if( isset($words[0]) ) {
    	    foreach($words[0] as $word) { 
    	        if(!is_string($word)) { 
    	            return false; 
    	        }
    	        
    	        // Look into Advertiser Catagoy Column
    	        // if following condition met, than no need to go further.
//     	        if(stripos($word,'ADC:')!==false) {
//     	            if(stripos($aData['advCat'],str_replace('ADC:', '', $word))!==false) {
//     	                return true;
//     	            }    	            
//     	        }
    	        
    	        $exitOnce = false;
    	        foreach(explode('|', $word) as $word) {
    	            if(stripos($aData,$word)!==false) { 
    	            	 $exitOnce = true; 
    	        	}    	        	
    	        }
    	        // not find even once
    	        if( !$exitOnce ) return false;
    	    }
	    }
	    return true;
	}
	
	protected function mapCategory( $col1, $col2=null )
	{
		if( !$this->isValidCategory($col1) ) {
			return false;
		}
		
		$aCat1 = $this->__mapCategory( $col1 );
		if($col2) $aCat2 = $this->__mapCategory( $col2 ); else $aCat2 = array();
		return array_unique(array_merge($aCat1, $aCat2));
	}
	
	private function __mapCategory( $sScrString )
	{
		$aFianlCategories = array();
		//return (isset($this->aMapping[ 'category' ][ $sCategory ]) ? $this->aMapping[ 'category' ][ $sCategory ] : false);
		
		if( stristr($sScrString, 'Shirt') ) {
			$aCategories = array(2,6); 			
			if( stristr($sScrString, 'T-Shirt') )  $aCategories[] = 32;
			if( stristr($sScrString, 'Sport') )  	$aCategories[] = 33;
			if( stristr($sScrString, 'Polos') )  	$aCategories[] = 34;
			if( stristr($sScrString, 'Dress') )  	$aCategories[] = 35;
			if( count($aCategories)==2 )							$aCategories[] = 71; // Misc.
			$aFianlCategories = array_merge($aFianlCategories, $aCategories);
		}
		
		if( stristr($sScrString, 'Pant') ) {
			$aCategories= array(2,7);
			if( stristr($sScrString, 'Dress') )  	$aCategories[] = 38;
			if( stristr($sScrString, 'Casual') )  	$aCategories[] = 39;
			if( stristr($sScrString, 'Jean') ) {
				$aCategories[] = 40;
				if( stristr($sScrString, 'Straight') )  	$aCategories[] = 49;
				if( stristr($sScrString, 'Slim') )  		$aCategories[] = 50;
				if( stristr($sScrString, 'Relaxed') )  	$aCategories[] = 51;
				if( stristr($sScrString, 'Premium') )  	$aCategories[] = 52;
				if( stristr($sScrString, 'Bootcut') )  	$aCategories[] = 53;
			}
			if( count($aCategories)==2 )							$aCategories[] = 72; // Misc.
			$aFianlCategories = array_merge($aFianlCategories, $aCategories);
		}
		
		if( stristr($sScrString, 'Suits') ) {
			array_push($aFianlCategories, 2,36);
			if( stristr($sScrString, 'Separate') )  	$aFianlCategories[] = 56;
			else  														$aFianlCategories[] = 55;
		}
		
		if( stristr($sScrString, 'Sweater') || stristr($sScrString, 'Sweatshirt') ) {
			array_push($aFianlCategories, 2,37);
		}	

		if( stristr($sScrString, 'Activewear') ) {
			$aCategories = array(2,57);
			if( stristr($sScrString, 'T-Shirt') )  $aCategories[] = 58;
			if( stristr($sScrString, 'Swim') )  	$aCategories[] = 59;
			if( stristr($sScrString, 'Shorts') )  	$aCategories[] = 59;
			if( stristr($sScrString, 'Pant') )  	$aCategories[] = 60;
			if( stristr($sScrString, 'Hood') )  	$aCategories[] = 61;
			if( stristr($sScrString, 'Jacket') )  	$aCategories[] = 61;
			if( count($aCategories)==2 )							$aCategories[] = 73; // Misc.
			$aFianlCategories = array_merge($aFianlCategories, $aCategories);
		}	
		
		
		if( stristr($sScrString, 'Underwear') ) {
			array_push($aFianlCategories, 2,68,69);
		}		
		
		if( stristr($sScrString, 'Socks') ) {
			array_push($aFianlCategories, 2,68,70);
		}		
		
		if( stristr($sScrString, 'Shoes') ) {
			$aCategories[] = 4;
			if( stristr($sScrString, 'Sneaker') )  	$aCategories[] = 41;
			if( stristr($sScrString, 'Sandal') )  	$aCategories[] = 42;
			if( stristr($sScrString, 'Dress') )  	$aCategories[] = 46;
			if( stristr($sScrString, 'Casual') )  	$aCategories[] = 47;
			if( stristr($sScrString, 'Boot') )  	$aCategories[] = 48;
			if( count($aCategories)==1 )			$aCategories[] = 74; // Misc.
			$aFianlCategories = array_merge($aFianlCategories, $aCategories);
		}	
		
		if( stristr($sScrString, 'Accessor') ) {
			$aCategories[] = 63;
			if( stristr($sScrString, 'Wallet') )  		$aCategories[] = 65;
			if( stristr($sScrString, 'Sunglasses') )  	$aCategories[] = 66;
			if( stristr($sScrString, 'Hat') )  			$aCategories[] = 67;
			if( stristr($sScrString, 'Belt') )  		$aCategories[] = 44;
			if( stristr($sScrString, 'Bag') )  			$aCategories[] = 3;
			if( stristr($sScrString, 'Ties') )  		$aCategories[] = 62;
			if( stristr($sScrString, 'Bows') )  		$aCategories[] = 64;
			if( stristr($sScrString, 'Watch') )  		$aCategories[] = 43;
			if( count($aCategories)==1 )				$aCategories[] = 75; // Misc.
			$aFianlCategories = array_merge($aFianlCategories, $aCategories);
		}
			
		return $aFianlCategories;
		
	}
	
	protected function extractBrand( $sHaystack )
	{
		foreach($this->aMapping['brand'] as $val) {
			if( stristr($sHaystack, $val) !== false ) {
				return $val;
			}
		}
		return '';
	}	
	
	protected function addVenueInfo( &$aData )
	{
	    if( isset($aData['attributes']['categories']) && count($aData['attributes']['categories']) ) {
	        
	       $aCats = array_flip($aData['attributes']['categories']);
	       

	       if( isset($aCats[35]) || isset($aCats[38]) || isset($aCats[56]) || isset($aCats[55]) || isset($aCats[46]) ) {
	           $aData['attributes']['venues'][] = 'BusinessFormal';
	       }	       
	       
	       if( isset($aCats[35]) || isset($aCats[38]) || isset($aCats[55]) || isset($aCats[56]) || isset($aCats[55]) || isset($aCats[46]) ) {
	           $aData['attributes']['venues'][] = 'BusinessProfessional';
	       }

	       if( isset($aCats[35]) || isset($aCats[38]) || isset($aCats[55]) || isset($aCats[56]) || isset($aCats[55]) || isset($aCats[46]) ) {
	           $aData['attributes']['venues'][] = 'BusinessProfessional';
	       }
	       
	       if( isset($aCats[32]) || isset($aCats[34]) || isset($aCats[40]) || isset($aCats[56]) || isset($aCats[37]) || isset($aCats[42]) || isset($aCats[42]) || isset($aCats[47]) ) {
	           $aData['attributes']['venues'][] = 'BusinessCasual';
	       }

	       if( isset($aCats[32]) || isset($aCats[33]) || isset($aCats[34]) || isset($aCats[39]) || isset($aCats[40]) || isset($aCats[37]) || isset($aCats[42]) || isset($aCats[47]) ) {
	           $aData['attributes']['venues'][] = 'Casual';
	       }	       	       
// 	       if( isset($aData['attributes']['venues']) && is_array( $aData['attributes']['venues']) ) {
// 	           $aData['attributes']['venues'] = array_unique( $aData['attributes']['venues']);
// 	           //print_r($aData['attributes']['venues']);
// 	           //exit(0);
// 	       }
	       
	    }
	}
	
	protected function extractColor( $sHaystack )
	{
		foreach($this->aMapping['color'] as $key => $val) {
			if( stristr($sHaystack, $key) !== false ) {
				return $val;
			}
		}
		return '';		
	}	

	protected function extractShirtSize( $str )
	{
		$pattern = '/(14|14.5|15|15.5|16|16.5|17|17.5|18|18.5|19|20|21|22) (X|-) 3[0-9]((-|\/)*[3-6][0-9])*/';
		preg_match($pattern, $str, $arr);
		if( isset($arr[0]) ) {
			return $arr[0];
		}
	
		$pattern = '/(XXX-Large|XX-Large|X-Small|X-Large|X-Long|Medium|Small|Large|[1-9]?XLT|[1-9]XB)/';
		preg_match($pattern, $str, $arr);
		if( isset($arr[0]) ) {
			return $arr[0];
		}
	
		$pattern = '/((\s|[0-9]|^)[X]*[SML](,|\s|\.|$))/';
		preg_match($pattern, $str, $arr);
		if( isset($arr[0]) ) {
			$arr[0] = trim(trim(trim($arr[0]),'.'),'.');
			return $arr[0];
		}
	
		$pattern = '/((,|\s|\.|^)(([0-9]?[0-9](\.5)?)|(LT)|([1-9]X))(,|\s|\.|$))/';
		preg_match($pattern, $str, $arr);
		if( isset($arr[0]) ) {
			$arr[0] = trim(trim(trim($arr[0]),'.'),'.');
			return $arr[0];
		}
	
		return "opps{$str}";
	}

	protected function extractPantSize( $str )
	{
		$pattern = '/((,|\s|\.|^)[0-9][0-9]((\s)?(-|x|X)(\s)?[0-9][0-9])*(,|\s|\.|$))/';
		preg_match($pattern, $str, $arr);
		if( isset($arr[0]) ) {
			return $arr[0];
		}
		
		$pattern = '/(,|\s|\.|^)(Small|Medium|X*-?Large|[1-9]XB|X*[SML]|[1-9]?X?LT)(,|\s|\.|$)/';
		preg_match($pattern, $str, $arr);
		if( isset($arr[0]) ) {
			return $arr[0];
		}
	
		return "opps{$str}";
	}	

	protected function extractShoeSize( $str )
	{
		$pattern = '/((,|\s|\.|^)[0-9][0-9]*(\.5)*[W]*(,|\s|\.|$))/';
		preg_match($pattern, $str, $arr);
		if( isset($arr[0]) ) {
			return $arr[0];
		}
	
		return "opps{$str}";
	}	
}
