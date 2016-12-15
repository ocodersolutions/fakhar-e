<?php

namespace Application\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Delete;
use Zend\Db\Sql\Update;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Expression;
use Zend\Paginator\Paginator;
use Zend\Db\ResultSet\ResultSet;
use Zend\Paginator\Adapter\Iterator as paginatorIterator;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Db\Sql\Where;

class ProductAttributesTable extends BasicTableAdapter {

    protected $productAttributesTable = 'ProductAttributes';
    protected $productReferenceAttributesTable = 'ProductReferenceAttributesCatMapping';


    public function getAttributes( $sUID ) {
        $sql = new Sql($this->getDBAdapter());
    
        $select = new Select();
        $select->from(array('t' => $this->productAttributesTable))
                ->where("t.productUID = '{$sUID}'");
    
//         /echo $sql->getSqlStringForSqlObject($select);die;
        $aAttributes = $sql->prepareStatementForSqlObject($select)->execute();

        $aReturnArray = array();
        foreach ($aAttributes as $att) {
            if( isset( $att['type'] )  ) {
                $aReturnArray[$att['type']][] = array('value'=>$att['value'],'attributeParentId'=> $att['attributeParentId'],'attributeId'=> $att['attributeId'],'type' =>$att['type']);
            }
        }
       // echo $select->getSqlString(); 

        return $aReturnArray;
    }

    public function getAttributesNoTree( $sUID ) {
        $sql = new Sql($this->getDBAdapter());
    
        $select = new Select();
        $select->from(array('t' => $this->productAttributesTable))
                ->where("t.productUID = '{$sUID}'")
                ->order("attributeParentId DESC");
    
        $aAttributes = $sql->prepareStatementForSqlObject($select)->execute();

        $aReturnArray = array();
        foreach ($aAttributes as $att) {
            $aReturnArray[] = array('value'=>$att['value'],'attributeParentId'=> $att['attributeParentId'],'attributeId'=> $att['attributeId'],'type' =>$att['type']);
        }

        return $aReturnArray;
    }
    
    public function getChildCount( $iParentID ) {
        $sql = new Sql($this->getDBAdapter());
    
        $select = new Select();
        $select->from(array('t' => $this->productAttributesTable))
                ->columns(array( 'chlidCount'=>new Expression("count(attributeId)")))
                ->where("t.attributeParentId = '{$iParentID}'");
    
        $aAttribute = $sql->prepareStatementForSqlObject($select)->execute()->current();    
        return (isset($aAttribute['chlidCount']) ? $aAttribute['chlidCount'] : 0);
    }    
    
    
    public function getAttributesTreeForUIMapped($iParentID = 0 ) {//echo "test"; die;
        $sql = new Sql($this->getDBAdapter());
        $select = new Select();
        $select->from(array('t' => 'ProductReferenceAttributesCatMapping'))
        ->where("t.attributeParentId = '{$iParentID}' and isActive = 'yes'")
        ->order("sortOrder");
    
        $tmp = $select;
        $tmp = $sql->prepareStatementForSqlObject($select)->execute()->current();
    
        //         if( $tmp['depth'] == 3 ) {
        //             $select->where("t.title = 'Type' and isActive = 'yes'");
        //             //echo $sql->getSqlStringForSqlObject($select);die;
    
        //             $aTypes = $sql->prepareStatementForSqlObject($select)->execute()->current();
        //             if( empty($aTypes['attributeId']) ) $aTypes['attributeId'] = '-1';
        //             $select = new Select();
        //             $select->from(array('t' => 'ProductReferenceAttributesCatMapping'))
        //                     ->where("t.attributeParentId = '{$aTypes['attributeId']}' and isActive = 'yes'")
        //                     ->order("sortOrder");
        //         }
    
    
        $parents = $sql->prepareStatementForSqlObject($select)->execute();
        $aReturnArray = array();
        foreach ($parents as $att) {
            $aReturnArray[$att['attributeId']] = array('id' => $att['attributeId'], 'title' => $att['title'], 'pid' => $att['attributeParentId'], 'depth' => $att['depth'], 'inputType' => $att['inputType']);
            if( $att['depth'] < 3 ) {
                $aReturnArray[$att['attributeId']]['child'] = $this->getAttributesTreeForUIMapped($att['attributeId']);
            }
        }
        return $aReturnArray;
    }    
     
    public function getAttributesTreeForUI($iParentID = 0 ) {
        $sql = new Sql($this->getDBAdapter());
        $select = new Select();
        $select->from(array('t' => $this->productReferenceAttributesTable))
                ->where("t.attributeParentId = '{$iParentID}' and isActive = 'yes'")
                ->order("sortOrder");
        
        $tmp = $select;        
        $tmp = $sql->prepareStatementForSqlObject($select)->execute()->current();
        
        if( $tmp['depth'] == 4 ) {
            $select->where("t.title = 'Type' and isActive = 'yes'");
            //echo $sql->getSqlStringForSqlObject($select);die;
        
            $aTypes = $sql->prepareStatementForSqlObject($select)->execute()->current();
            if( empty($aTypes['attributeId']) ) $aTypes['attributeId'] = '-1';
            $select = new Select();
            $select->from(array('t' => $this->productReferenceAttributesTable))
                    ->where("t.attributeParentId = '{$aTypes['attributeId']}' and isActive = 'yes'")
                    ->order("sortOrder");
        }        
        
        
        $parents = $sql->prepareStatementForSqlObject($select)->execute();
        $aReturnArray = array();
        foreach ($parents as $att) {
            $aReturnArray[$att['attributeId']] = array('id' => $att['attributeId'], 'title' => $att['title'], 'pid' => $att['attributeParentId'], 'depth' => $att['depth'], 'inputType' => $att['inputType']);
            if( $att['depth'] < 3 ) {
                $aReturnArray[$att['attributeId']]['child'] = $this->getAttributesTreeForUI($att['attributeId']);
            }
        }
        return $aReturnArray;
    }    

    public function getAttributesTree($iParentID = 0, $bAddBrand = false, $iParentCol='') {
        $sql = new Sql($this->getDBAdapter());
        $select = new Select();
        $select->from(array('t' => $this->productReferenceAttributesTable))
                ->where("t.attributeParentId{$iParentCol} = '{$iParentID}' and isActive{$iParentCol} = 'yes'")
                ->order("sortOrder");
        $parents = $sql->prepareStatementForSqlObject($select)->execute();
        $aReturnArray = array();
        foreach ($parents as $att) {
            $aReturnArray[$att['attributeId']] = array('id' => $att['attributeId'], 'title' => $att['title'], 'pid' => $att["attributeParentId{$iParentCol}"], 'depth' => $att['depth'], 'inputType' => $att['inputType']);
            

            //if( !$bAddBrand && $att['depth'] == 3 &&  $att['title'] == 'Brand'  )  {
                //$aReturnArray[$att['attributeId']]['child'] = $this->getBrandsForAttributesTree(); 
                //continue;
            //}    
            //else {   
                if($att['depth'] == 4)      $aReturnArray[$att['attributeId']]['child'] = array();
                else                        $aReturnArray[$att['attributeId']]['child'] = $this->getAttributesTree($att['attributeId'], $bAddBrand, $iParentCol);
            //}
            //$a =1;
        }
        return $aReturnArray;
    }
    
    private function getBrandsForAttributesTree()
    {
        $sql = new Sql($this->getDBAdapter());
        $select = new Select();
        $select->from(array('t' => $this->FeedsMapping))->where("t.mappingType = 'brand'");
        $parents = $sql->prepareStatementForSqlObject($select)->execute();
        $aReturnArray = array();
        foreach ($parents as $att) {
            $aReturnArray[$att['attributeId']] = array('id' => $att['attributeId'], 'title' => $att['title'], 'pid' => $att["attributeParentId{$iParentCol}"], 'depth' => $att['depth'], 'inputType' => $att['inputType']);
        
        
            if( !$bAddBrand && $att['depth'] == 3 &&  $att['title'] == 'Brand'  )  {
                continue;
            }
            else {
                $aReturnArray[$att['attributeId']]['child'] = $this->getAttributesTree($att['attributeId'], $bAddBrand, $iParentCol);
            }
            $a =1;
        }
        return $aReturnArray;        
    }

    public function getAttributesLevel2() {
        $sql = new Sql($this->getDBAdapter());
        $select = new Select();
        $select->from(array('t' => $this->productReferenceAttributesTable))
                ->where("t.depth = '2' and attributeParentId= '2' and isActive = 'yes'");
        $parents = $sql->prepareStatementForSqlObject($select)->execute();
        $aReturnArray = array();
        foreach ($parents as $att) {
            $aReturnArray[] = $att;
           
        }
        return $aReturnArray;
    }
    public function getAttributesLevel3($attributeParentId = null) {
        $sql = new Sql($this->getDBAdapter());
        $select = new Select();
        $select->from(array('t' => $this->productReferenceAttributesTable))
                ->where("t.depth = '3' and attributeParentId= '{$attributeParentId}' and isActive = 'yes' and mandatory = '1'");
        $parents = $sql->prepareStatementForSqlObject($select)->execute();
        $aReturnArray = array();
        foreach ($parents as $att) {
            $aReturnArray[] = $att;
           
        }
        return $aReturnArray;
    }
    // public function getAttributeParentId($id=38){
    //     $sql = new Sql($this->getDBAdapter());
    //     $select = new Select();
    //     $select->from(array('t' => $this->productReferenceAttributesTable))
    //             ->where("t.attributeId = '{$id}'");
    //     $parents = $sql->prepareStatementForSqlObject($select)->execute();

    //     $aReturnArray = array();
    //     foreach ($parents as $att) {
    //         $aReturnArray[$att['attributeParentId']] = $this->getAttributeParentId($att['attributeParentId']);
       
    //     }
    //     return $aReturnArray;
    // }

//     public function addProductAttributes($iProductUId, $aAttributes) {
//         $oAdapter = $this->getDBAdapter();
//         $sql = new Sql($oAdapter);

//         //nếu có màu thì xóa màu rỗng
// 		if(count($aAttributes['color']) > 0 ){
//             $delete = new Delete();
//             $where ="productUID = '{$iProductUId}' AND type = 'color'";
//             $delete->from($this->productAttributesTable)
//                     ->where($where);
//             $sql->prepareStatementForSqlObject($delete)->execute();
//         }

// 		foreach($aAttributes as $key => $val) {
// 			if( is_array($val) ) {
// 				foreach($val as $iAttribute) {
// 					$oInsert = $sql->insert( $this->productAttributesTable )->values( array('productUID'=>$iProductUId, 'type'=>$key, 'value'=>$iAttribute) );
// 					$sql->prepareStatementForSqlObject( $oInsert );
// 					$sInsertString = $oInsert->getSqlString($oAdapter->getPlatform());
// 					$oAdapter->query(  "$sInsertString ON DUPLICATE KEY UPDATE attribute_count = attribute_count + 1"  )->execute();
// 				}
// 			}
// 			else{
// 				$oInsert = $sql->insert( $this->productAttributesTable )->values( array('productUID'=>$iProductUId, 'type'=>$key, 'value'=>$val) );
// 				$sql->prepareStatementForSqlObject( $oInsert );
// 				$sInsertString = $oInsert->getSqlString($oAdapter->getPlatform());
// 				$oAdapter->query(  "$sInsertString ON DUPLICATE KEY UPDATE attribute_count = attribute_count + 1"  )->execute();
// 			}
// 		}

// // 		foreach($aAttributes['categories'] as $iAttribute) {
// // 			$oInsert = $sql->insert( $this->productAttributesTable )->values( array('productUID'=>$iProductUId, 'type'=>'category', 'value'=>$iAttribute) );
// // 			$sql->prepareStatementForSqlObject( $oInsert );
// // 			$sInsertString = $oInsert->getSqlString($oAdapter->getPlatform());
// // 			$oAdapter->query(  "$sInsertString ON DUPLICATE KEY UPDATE attribute_count = attribute_count + 1"  )->execute();
// // 		}
		
// // 		foreach($aAttributes['color'] as $iAttribute) {
// // 			$oInsert = $sql->insert( $this->productAttributesTable )->values( array('productUID'=>$iProductUId, 'type'=>'color', 'value'=>$iAttribute) );
// // 			$sql->prepareStatementForSqlObject( $oInsert );
// // 			$sInsertString = $oInsert->getSqlString($oAdapter->getPlatform());
// // 			$oAdapter->query(  "$sInsertString ON DUPLICATE KEY UPDATE attribute_count = attribute_count + 1"  )->execute();
						
// // 		}

// // 		foreach($aAttributes['size'] as $iAttribute) {
// // 			$oInsert = $sql->insert( $this->productAttributesTable )->values( array('productUID'=>$iProductUId, 'type'=>'size', 'value'=>$iAttribute) );
// // 			$sql->prepareStatementForSqlObject( $oInsert );
// // 			$sInsertString = $oInsert->getSqlString($oAdapter->getPlatform());
// // 			$oAdapter->query(  "$sInsertString ON DUPLICATE KEY UPDATE attribute_count = attribute_count + 1"  )->execute();
		
// // 		}		

// 	}	

    public function addProductAttributes_Categories($iProductUId, $iCategory) {
        
        $aRefAtrribs = $this->getAttrbyCondition($iCategory);
        $oAdapter = $this->getDBAdapter();
        $sql = new Sql($oAdapter);

                $oInsert = $sql->insert( $this->productAttributesTable )->values( 
                        array(
                                'productUID'        => $iProductUId, 
                                'type'              => $aRefAtrribs['type'], 
                                'value'             => $aRefAtrribs['value'],   
                                'attribute_count'   => 1,
                                'attributeParentId' => $aRefAtrribs['attributeParentId'], 
                                'attributeId'       => $aRefAtrribs['attributeId'], 
                        ) 
                    );
                $sql->prepareStatementForSqlObject( $oInsert );
                $sInsertString = $oInsert->getSqlString($oAdapter->getPlatform());
                $oAdapter->query(  "$sInsertString ON DUPLICATE KEY UPDATE attribute_count = attribute_count + 1"  )->execute();
    
    }

    public function addProductAttributes_Attributes($iProductUId, $iAttribute, $iValue) {
    
        $aRefAtrribs = $this->getAttrbyCondition($iAttribute);
        $aRefValues = $this->getAttrbyCondition($iValue);
        $oAdapter = $this->getDBAdapter();
        $sql = new Sql($oAdapter);
    
        $oInsert = $sql->insert( $this->productAttributesTable )->values(
            array(
                'productUID'        => $iProductUId,
                'type'              => $aRefAtrribs['type'],
                'value'             => $aRefAtrribs['value'],
                'attribute_count'   => 1,
                'attributeParentId' => $aRefAtrribs['attributeParentId'],
                'attributeId'       => $aRefAtrribs['attributeId'],
            )
        );
        $sql->prepareStatementForSqlObject( $oInsert );
        $sInsertString = $oInsert->getSqlString($oAdapter->getPlatform());
        $oAdapter->query(  "$sInsertString ON DUPLICATE KEY UPDATE attribute_count = attribute_count + 1"  )->execute();
    
    }
        public function addProductAttributes($iProductUId, $aAttributes) {
        $oAdapter = $this->getDBAdapter();
        $sql = new Sql($oAdapter);

        //nếu có chon màu thì xóa màu rỗng
//         if(isset($aAttributes['Color']) && count($aAttributes['Color']) > 0 ){
//             $delete = new Delete();
//             $where ="productUID = '{$iProductUId}' AND type = 'color'";
//             $delete->from($this->productAttributesTable)
//                     ->where($where);
//             $sql->prepareStatementForSqlObject($delete)->execute();
//         }

        foreach($aAttributes as $key => $val) {
            if( is_array($val) ) {
                foreach($val as $iAttribute) {

                    $oInsert = $sql->insert( $this->productAttributesTable )->values( array('productUID'=>$iProductUId, 'type'=>$key, 'value'=>$iAttribute['value'],'attributeParentId' =>$iAttribute['attributeParentId'] ,'attributeId' =>$iAttribute['attributeId'] ) );
                    $sql->prepareStatementForSqlObject( $oInsert );
                    $sInsertString = $oInsert->getSqlString($oAdapter->getPlatform());
                    $oAdapter->query(  "$sInsertString ON DUPLICATE KEY UPDATE attribute_count = attribute_count + 1"  )->execute();

                }
            }
            // else{
            //     $oInsert = $sql->insert( $this->productAttributesTable )->values( array('productUID'=>$iProductUId, 'type'=>$key, 'value'=>$val) );
            //     $sql->prepareStatementForSqlObject( $oInsert );
            //     $sInsertString = $oInsert->getSqlString($oAdapter->getPlatform());
            //     $oAdapter->query(  "$sInsertString ON DUPLICATE KEY UPDATE attribute_count = attribute_count + 1"  )->execute();
            // }
        }      

    }   
    
    public function deleteProductAttributes($iProductUId, $type){
        $sql = new Sql($this->getDBAdapter());
        $delete = new Delete();
        $where ="productUID = '{$iProductUId}' AND type = '{$type}'";
        $delete->from($this->productAttributesTable)
        ->where($where);
        $sql->prepareStatementForSqlObject($delete)->execute();
        //echo $sql->getSqlStringForSqlObject($delete );die;
        // if($type =='color') {
        $sql = new Sql($this->getDBAdapter());
        $insert = $sql->insert( $this->productAttributesTable )->values( array('productUID'=>$iProductUId, 'type'=>'color', 'value'=>''));
        $sql->prepareStatementForSqlObject($insert)->execute();
        //}
    }    

    public function deleteUnUsedAttributes(){
        
        $sql = new Sql($this->getDBAdapter());
        $delete = new Delete();
        $delete->from($this->productAttributesTable)->where("attribute_count = 0");
        $sql->prepareStatementForSqlObject($delete)->execute();
       
    }
        public function deleteAllProductAttributes($iProductUId){
             $sql = new Sql($this->getDBAdapter());
             $delete = new Delete();
             $where ="productUID = '{$iProductUId}'";
             $delete->from($this->productAttributesTable)
                    ->where($where);
            $sql->prepareStatementForSqlObject($delete)->execute();
            //echo $sql->getSqlStringForSqlObject($delete );die;
           
//             $sql = new Sql($this->getDBAdapter());
//             $insert = $sql->insert( $this->productAttributesTable )->values( array('productUID'=>$iProductUId, 'type'=>'color', 'value'=>''));
//             $sql->prepareStatementForSqlObject($insert)->execute();
                
    }

    public function reset( $iFeedId ) {
        $sQuery = "
            update FeedData d
            INNER JOIN ProductAttributes a on d.uid = a.productUID
            set a.attribute_count = 0
            where d.feedId = {$iFeedId}
		    ";
        
        $statement = $this->getDBAdapter()->query($sQuery);
        $oMapping = $statement->execute();        
    }
    
    public function getLevel1CategoryIds($iParentCol='') 
    {
        $sql = new Sql($this->getDBAdapter());
        $select = new Select();
        $select->from(array('t' => $this->productReferenceAttributesTable))
                ->where("t.attributeParentId{$iParentCol} = '1' and isActive{$iParentCol} = 'yes'")
                ->order("sortOrder");
        $parents = $sql->prepareStatementForSqlObject($select)->execute();
        $aReturnArray = array();
        foreach ($parents as $att) {
            $aReturnArray[] = $att['attributeId'];
        }
        return $aReturnArray;
    } 
    
    public function getLevel2CategoryIds($iParentCol='')
    {
        $ids = $this->getLevel1CategoryIds();
        
        $sql = new Sql($this->getDBAdapter());
        $select = new Select();
        $select->from(array('t' => $this->productReferenceAttributesTable))
                ->where("t.attributeParentId{$iParentCol} in (".implode(',', $ids).") and isActive{$iParentCol} = 'yes'")
                ->order("sortOrder");
        $parents = $sql->prepareStatementForSqlObject($select)->execute();
        $aReturnArray = array();
        foreach ($parents as $att) {
            $aReturnArray[] = $att['attributeId'];
        }
        return $aReturnArray;
    }       

    public function getCategoryTypeIds()
    {
        $sql = new Sql($this->getDBAdapter());
        $select = new Select();
        $select->from(array('t' => $this->productReferenceAttributesTable))
                ->where("t.title = 'Type' and isActive = 'yes'")
                ->order("sortOrder");
        $parents = $sql->prepareStatementForSqlObject($select)->execute();
        $aReturnArray = array();
        foreach ($parents as $att) {
            $aReturnArray[] = $att['attributeId'];
        }
    
        $sql = new Sql($this->getDBAdapter());
        $select = new Select();
        $select->from(array('t' => $this->productReferenceAttributesTable))
            ->where("t.attributeParentId in (".implode(',', $aReturnArray).") and isActive = 'yes'")
            ->order("sortOrder");
        $parents = $sql->prepareStatementForSqlObject($select)->execute();
        $aReturnArray = array();
        foreach ($parents as $att) {
            $aReturnArray[] = $att['attributeId'];
        }
        return $aReturnArray;
    }
    
    public function getFeedDataSizes($aParams = array()) {
        $isWhere = ' 1 ';
        if (!empty($aParams['productUID'])) {
            $isWhere .= " AND productUID='{$aParams['productUID']}'";
        }

        if (!empty($aParams['type'])) {
            $isWhere .= " AND type='{$aParams['type']}'";
        }

        if (!empty($aParams['value'])) {
            $isWhere .= " AND value='{$aParams['value']}'";
        }

        $sql = new Sql($this->getServiceLocator()->get('db'));
        $select = $sql->select(array('prod_attr' => $this->productAttributesTable));
        $select->where($isWhere);
        //echo $sql->getSqlStringForSqlObject($select);die;
        $resultSet = array();
        $results = $sql->prepareStatementForSqlObject($select)->execute();
        $resultSet = new \Zend\Db\ResultSet\ResultSet();
        $resultSet->initialize($results);
        $resultSet = $resultSet->toArray();
        return $resultSet;
    }

    public function getAttrbyCondition($id){
        $sql = new Sql($this->getDBAdapter());
        $select = new Select();
        $select->from(array('t' => $this->productReferenceAttributesTable))
                ->where("t.attributeId = '{$id}'");
    
        $results = $sql->prepareStatementForSqlObject($select)->execute()->current();
        //return \Zend\Stdlib\ArrayUtils::iteratorToArray($results);
        // $resultSet = new \Zend\Db\ResultSet\ResultSet();
        // $resultSet->initialize($results);
        // $resultSet = $resultSet->toArray();
        return $results;
    }

}

/*
update ProductReferenceAttributes t1
left join ProductReferenceAttributes t2 on t1.attributeParentId = t2.attributeId
left join ProductReferenceAttributes t3 on t2.attributeParentId = t3.attributeId
left join ProductReferenceAttributes t4 on t3.attributeParentId = t4.attributeId
left join ProductReferenceAttributes t5 on t4.attributeParentId = t5.attributeId
left join ProductReferenceAttributes t6 on t5.attributeParentId = t6.attributeId
left join ProductReferenceAttributes t7 on t5.attributeParentId = t7.attributeId
left join ProductReferenceAttributes t8 on t5.attributeParentId = t8.attributeId
left join ProductReferenceAttributes t9 on t5.attributeParentId = t9.attributeId
left join ProductReferenceAttributes t10 on t5.attributeParentId = t10.attributeId
set t1.depth = 
case
	when t2.attributeParentId is null then 0
	when t3.attributeParentId is null then 1
	when t4.attributeParentId is null then 2
	when t5.attributeParentId is null then 3
	when t6.attributeParentId is null then 4
	when t7.attributeParentId is null then 5
	when t8.attributeParentId is null then 6
	when t9.attributeParentId is null then 7
	when t10.attributeParentId is null then 8
	else 'error'
end ;
*/