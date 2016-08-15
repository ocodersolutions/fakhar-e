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

class FeedMappingTable extends BasicTableAdapter {

    protected $table = 'FeedsMapping';
    public function getFeedMapping($aParams=array()) {
        $isWhere = ' 1 ';
        if (!empty($aParams['mappingType'])) {
            $isWhere .= " AND mappingType='{$aParams['mappingType']}'";
        }
        
        if (!empty($aParams['id'])) {
            $isWhere .= " AND id='{$aParams['id']}'";
        }
        
        if (!empty($aParams['title'])) {
            $isWhere .= " AND title='{$aParams['title']}'";
        }
        
        if (!empty($aParams['top'])) {
            $isWhere .= " AND top='1' ";
        }
        
        if (!empty($aParams['feedId'])) {
            $isWhere .= " AND feedId={$aParams['feedId']}";
        }
        $sql = new Sql($this->getServiceLocator()->get('db'));
        $select = $sql->select(array('feedmapping' => $this->table));
        $select->where($isWhere);
        $select->order('title');
        $resultSet = array();
        $results = $sql->prepareStatementForSqlObject($select)->execute();
        $resultSet = new \Zend\Db\ResultSet\ResultSet();
        $resultSet->initialize($results);
        $resultSet = $resultSet->toArray();
        return $resultSet;
    }
    
    public function getDistinctColor()
    {
        $sql = new Sql($this->getServiceLocator()->get('db'));
        $select = $sql->select(array('feedmapping' => $this->table));
        $select->columns(array('value'=>new Expression('DISTINCT value')))
                ->where('mappingType="color"');
        $resultSet = array();
        //echo $sql->getSqlStringForSqlObject($select);die;
        $results = $sql->prepareStatementForSqlObject($select)->execute();
        $resultSet = new \Zend\Db\ResultSet\ResultSet();
        $resultSet->initialize($results);
        $resultSet = $resultSet->toArray();
        return $resultSet;
    }

}