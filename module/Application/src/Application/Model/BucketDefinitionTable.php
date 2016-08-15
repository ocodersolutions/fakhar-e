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
use Zend\Db\Adapter\Driver\Pdo\Result;

class BucketDefinitionTable extends BasicTableAdapter {

    protected $bucketdefinitionTable = 'bucketdefinitions';


    public function addDefinition($params,$option = null) {
    
        $oAdapter = $this->getDBAdapter();
        $sql = new Sql($oAdapter);

        $oInsert = $sql->insert( $this->bucketdefinitionTable )->values(
            array(
                'bucketID'          => $params['bucketID'],
                'title'             => $params['title'],
                'timestamp'         => date('Y-m-d H:i:s'),
                'userid'            => $params['userid'], 
            )
        );
        $sql->prepareStatementForSqlObject( $oInsert )->execute();
        $id =  $oAdapter->getDriver()->getLastGeneratedValue();
        return $id;
    }

    public function getDefinition($params = null){
            $sql = new Sql($this->getDBAdapter());
            $select = new Select();
            $select->from(array('t' => $this->bucketdefinitionTable))
                    ->order('timestamp DESC');
            $listItem = $sql->prepareStatementForSqlObject($select)->execute();

            $aReturnArray = array();
            foreach ($listItem as $item) {
               //$aReturnArray[$item['bucketID']][] = $item;
                $aReturnArray[]= $item;
            }
            return $aReturnArray;
    }
    

    public function getDefinitionWithAttributes( $sBucketID ){
        $sql = new Sql($this->getDBAdapter());
        $select = new Select();
        $select->from(array('d' => $this->bucketdefinitions));
        $select->join(array('a' => 'BucketAttributes'), new Expression("(d.id = a.bucketDefinitionID)"), array('a.*') );
        $listItem = $sql->prepareStatementForSqlObject($select)->execute(); 
    
        $aReturnArray = array();
        foreach ($listItem as $item) {
            $aReturnArray[]= $item;
        }
        return $aReturnArray;
    }    
    public function deleteDefinition($params){
             $sql = new Sql($this->getDBAdapter());
             $delete = new Delete();
             $where ="id = {$params['id']}";
             $delete->from($this->bucketdefinitionTable)
                    ->where($where);
            $sql->prepareStatementForSqlObject($delete)->execute();
    }

    public function editDefinition($id, $params = null){

            $sql = new Sql($this->getDBAdapter());
            $update = new Update();
            $update->table($this->bucketdefinitionTable)
                ->set($params)
                ->where("id = {$id}");
            $itemid = $sql->prepareStatementForSqlObject($update)->execute();
    
        return $itemid;
          
    }

    public function getAttributes( $sUID ) {
        $sql = new Sql($this->getDBAdapter());
    
        $select = new Select();
        $select->from(array('t' => $this->productAttributesTable))
                ->where("t.productUID = '{$sUID}'");
    
        $aAttributes = $sql->prepareStatementForSqlObject($select)->execute();

        $aReturnArray = array();
        foreach ($aAttributes as $att) {
            if( isset( $att['type'] )  ) {
                $aReturnArray[$att['type']][] = array('value'=>$att['value'],'attributeParentId'=> $att['attributeParentId'],'attributeId'=> $att['attributeId']);
            }
        }

        return $aReturnArray;
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
        public function deleteAllProductAttributes($bucketdefinitionsID){
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
    
    public function getLevel1CategoryIds() 
    {
        $sql = new Sql($this->getDBAdapter());
        $select = new Select();
        $select->from(array('t' => $this->productReferenceAttributesTable))
                ->where("t.attributeParentId = '1' and isActive = 'yes'")
                ->order("sortOrder");
        $parents = $sql->prepareStatementForSqlObject($select)->execute();
        $aReturnArray = array();
        foreach ($parents as $att) {
            $aReturnArray[] = $att['attributeId'];
        }
        return $aReturnArray;
    } 
    
    public function getLevel2CategoryIds()
    {
        $ids = $this->getLevel1CategoryIds();
        
        $sql = new Sql($this->getDBAdapter());
        $select = new Select();
        $select->from(array('t' => $this->productReferenceAttributesTable))
                ->where("t.attributeParentId in (".implode(',', $ids).") and isActive = 'yes'")
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
    
    public function addProductsToBuckets()
    {
        //return  true;
        $oUserProfile = new UserProfilesTable( $this->getTableGateway() );
        $oUserProfile->setServiceLocator( $this->getServiceLocator() );
        $aUserProfile = $oUserProfile->getUserProfile();
        
        $sql = new Sql($this->getDBAdapter());
        
        $oAttributes = $this->getServiceLocator()->get('ProductAttributesTable');
        
        $select = new Select();
        $select->from(array("d" => 'bucketdefinitions'))
            ->join(array("a" => 'BucketAttributes'), new Expression("d.id = a.bucketDefinitionID"), array('*') )
            ->order('bucketDefinitionID, type, attributeId desc')
        //->where("bucketID in ('".implode("','", $aStyles)."')")
        ;
         //echo $sql->getSqlStringForSqlObject($select); die;  
        $oData = $sql->prepareStatementForSqlObject($select)->execute();
        $aStyles = array(); $aBuckets = array();
        foreach($oData as $val) {
            if( isset($val['bucketDefinitionID']) ) {
                $aStyles[ $val['bucketDefinitionID'] ][ $val['type'] ][] = $val;
                $aBuckets[ $val['bucketDefinitionID'] ] = $val['bucketID'];
            }
        }
        //print_r($aStyles); die; 
        //print_r($aBuckets); die;
        $aSqlString = array();
        
        $dbAdpr = $this->getDBAdapter();
        $dbAdpr->query( "TRUNCATE bucketProducts_for_processing;" )->execute();
        
        $i = 0;
        foreach($aStyles as $sStyleKey => $sStyleVal) {
            
            if (!in_array($aBuckets[$sStyleKey], array('casual__CasualHipster','casual__CasualLeisure','casual__CasualOffice','casual__CasualPreppy','night_out__NightOutHipster','night_out__NightOutLeisure','night_out__NightOutOffice','night_out__NightOutPreppy','work__WorkHipster','work__WorkLeisure','work__WorkOffice','work__WorkPreppy','never_wear__FormalWear','never_wear__Pink','never_wear__SkinnyJeans','never_wear__Sporty'))){
                continue;
            }
        
            $sQuery = '';
            //$sQuery = ' (SELECT count(distinct ProductUID), #BUCKET_ID# FROM ProductAttributes where ( #WHERE_CLAUSE# ))';
        
            $aStyleOne = array();
            foreach($sStyleVal as $sSubKey => $sSubVal) {
                if( $sSubKey == 'categories' ) {
                    foreach($sSubVal as $v) {
                        
//                         if( empty($sQuery) ) {
//                             $sQuery = "select * from ProductAttributes where productUID in ( select distinct productUID from ProductAttributes  where type = 'categories' and value = {$v['value']})";
//                         }
//                         else {
//                             $sQuery = "	select * from (
//                             {$sQuery}
//                             ) t where productUID in ( select distinct productUID from ProductAttributes  where type = 'categories' and value = {$v['value']})";
//                         }
                        
                        
                        if($oAttributes->getChildCount( $v['value'] ) == 0 ) {
                            $aStyleOne[] = $v['value'];

                        }
                    }
                }
            }

            if( count($aStyleOne) ) {
               $sCondition = $v['condition'] == 'inclusive' ? ' in ' : ' not in ';
               $sQuery = "select * from ProductAttributes where productUID in ( select distinct productUID from ProductAttributes  where type = 'categories' and value {$sCondition} (".implode(',', $aStyleOne).") )";
               //break 2;
            }
                        
            reset( $sStyleVal );
            
            foreach($sStyleVal as $sSubKey => $sSubVal) {
                if( $sSubKey != 'categories' ) {
                    
                    $aTemp = array();
                    foreach ($sSubVal as $v) {
                        $aTemp[] = $v['value'];
                    }
                    
                    if( !empty($sQuery) && count($aTemp) ) {
//                         $sQuery = "select * from ProductAttributes where productUID in ( select distinct productUID from ProductAttributes  where type = '{$sSubKey}' and value in ('".implode("','", $aTemp)."') )";
//                     }
//                     else {
                        
                        $sCondition = $v['condition'] == 'inclusive' ? ' in ' : ' not in ';
                        
                        $sQuery = "	select * from (
                        {$sQuery}
                        ) t where productUID in ( select distinct productUID from ProductAttributes  where type = '{$sSubKey}' and value {$sCondition} ('".implode("','", $aTemp)."') )";
                    }
                     
                }
            }

            
            //echo $sQuery; die; 
        
            if( !empty($sQuery)  ) {
                $sQuery = "INSERT IGNORE INTO bucketProducts_for_processing (productUID, {$aBuckets[$sStyleKey]})
                SELECT DISTINCT productUID,  1 as 'BucketID' from ( {$sQuery} )t
                ON DUPLICATE KEY UPDATE {$aBuckets[$sStyleKey]} = 1
                ;";
                $dbAdpr->query( $sQuery )->execute();
                //echo $sQuery . "\n\n\n";
            }
            //if( $i++ >=2 ) die;
        }
        
        if( count($aStyles) ) {
            $dbAdpr->query( 'DROP TABLE IF EXISTS `bucketProducts_temp`;' )->execute();
            $dbAdpr->query( 'ALTER TABLE `bucketProducts` RENAME TO  `bucketProducts_temp` ;' )->execute();
            $dbAdpr->query( 'ALTER TABLE `bucketProducts_for_processing` RENAME TO  `bucketProducts` ;' )->execute();
            $dbAdpr->query( 'ALTER TABLE `bucketProducts_temp` RENAME TO  `bucketProducts_for_processing` ;' )->execute();      
        }  
        
        die;    
    }

}

