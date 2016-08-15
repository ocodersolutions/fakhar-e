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

class BucketAttributeTable extends BasicTableAdapter {

    protected $bucketAttributesTable = 'BucketAttributes';
    protected $productReferenceAttributesTable = 'ProductReferenceAttributes';


    public function getAttributes( $bdid ) {
        $sql = new Sql($this->getDBAdapter());

        $select = new Select();
        $select->from(array('t' => $this->bucketAttributesTable))
        ->where("t.bucketDefinitionID = '{$bdid}'");

        $aAttributes = $sql->prepareStatementForSqlObject($select)->execute();

        $aReturnArray = array();
        foreach ($aAttributes as $att) {
            if( isset( $att['type'] )  ) {
                $aReturnArray[$att['type']][] = array('value'=>$att['value'],'attributeParentId'=> $att['attributeParentId'],'attributeId'=> $att['attributeId'],'condition'=> $att['condition']);
            }
        }

        return $aReturnArray;
    }

    public function deleteAllBucketAttributes($bucketdefinitionID){
        $sql = new Sql($this->getDBAdapter());
        $delete = new Delete();
        $where ="bucketDefinitionID = '{$bucketdefinitionID}'";
        $delete->from($this->bucketAttributesTable)
               ->where($where);
               
        $sql->prepareStatementForSqlObject($delete)->execute();
    }

    public function addBucketAttributes_Categories($bucketDefinitionID, $iCategory, $sCondition='inclusive') {
        
        $aRefAtrribs = $this->getAttrbyCondition($iCategory);
        $oAdapter = $this->getDBAdapter();
        $sql = new Sql($oAdapter);

                $oInsert = $sql->insert( $this->bucketAttributesTable )->values( 
                        array(
                                'bucketDefinitionID'        => $bucketDefinitionID, 
                                'type'              => $aRefAtrribs['type'], 
                                'value'             => $aRefAtrribs['value'],   
                                'attribute_count'   => 1,
                                'attributeParentId' => $aRefAtrribs['attributeParentId'], 
                                'attributeId'       => $aRefAtrribs['attributeId'], 
                                'condition'         => $sCondition,
                        ) 
                    );
                $sql->prepareStatementForSqlObject( $oInsert );
                $sInsertString = $oInsert->getSqlString($oAdapter->getPlatform());
                $oAdapter->query(  "$sInsertString ON DUPLICATE KEY UPDATE attribute_count = attribute_count + 1"  )->execute();
    
    }

    public function getAttrbyCondition($id){
        $sql = new Sql($this->getDBAdapter());
        $select = new Select();
        $select->from(array('t' => $this->productReferenceAttributesTable))
                ->where("t.attributeId = '{$id}'");
    
        $results = $sql->prepareStatementForSqlObject($select)->execute()->current();
        return $results;
    }
}
