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

class OutfitBoughtTable extends BasicTableAdapter {

    protected $table = 'OutfitBought';
    
    public function getOutfits($arrParam){
        try {
            if ($arrParam != null) {
                $result = $this->tableGateway->select(function (Select $select) use ($arrParam) {
                    if (isset($arrParam['cols'])) {
                        $select->columns($arrParam['cols']);
                    }
                    if (isset($arrParam['limit'])) {
                        $select->limit($arrParam['limit']);
                    }
                    if (isset($arrParam['offset'])) {
                        $select->offset($arrParam['offset']);
                    }
                    if (isset($arrParam['where'])) {
                        $select->where($arrParam['where']);
                    }
                    if (isset($arrParam['order'])) {
                        $select->order($arrParam['order']);
                    }
                    if (isset($arrParam['join'])) {
                        foreach ($arrParam['join'] as $join) {
                            $select->join($join['table'], $join['on'], $join['field'], $join['type']);
                        }
                    }
                });
            } else {
                $result = $this->tableGateway->select();
                $result->buffer();
            }
            return $result->toArray();
        } catch (Exception $e) {
            return null;
        }
    }
    // Get Outfit
    public function getArticleLikes($id, $userid) {
        $id = (int) $id;
        $rowset = $this->tableGateway->select(array('outfeedid' => $id, 'userId' => $userid));
        $row = $rowset->current();
        return $row;
    }

    public function saveArticleLiekes($articleLikes) {
        $data = array(
            'outfeedid' => $articleLikes['articleId'],
            'userId' => $articleLikes['userId'],
        );
        $result = $this->tableGateway->insert($data);
        return $result;
    }

    public function getArticleLikesCount($id) {
        $id = (int) $id;
        $rowset = $this->tableGateway->select(array('outfeedid' => $id));
        $count = $rowset->count();
        return $count;
    }
    
    public function getOutFitCount() {
        $rowset = $this->tableGateway->select();
        $count = $rowset->count();
        return $count;
    }
    
    public function getOutfitsProducts($conditions = array(), $searchKey = null, $remdomone = null, $filterarg = null,$userId = null) {
        $sql = new Sql($this->getServiceLocator()->get('db'));
        $select = $sql->select(array('op' => 'outfits_products'))
                      ->join(array("Ol" => 'OutfitBought'), new Expression("(Ol.outfeedid = op.outfit_id)"),array(), 'left');
        $select->where(array('Ol.userId' => $userId));

        //$select->columns(array('value'=>new Expression('DISTINCT value')))
        foreach ($conditions as $colom => $value) {
            $select->where(array('op.' . $colom => $value));
        }

        if (isset($filterarg) && !empty($filterarg)) {
            $predicates = array();
            $i = 0;
            foreach ($filterarg as $value) {
//                 if ($i == 0) {
//                     $select->where->AND->like('venue', '%' . $value . '%');
//                 } else 
                {
                    if (isset($value) && !empty($value)) {
                        //$select->where->OR->like('venue', '%' . $value . '%');
                        $predicates[] = new \Zend\Db\Sql\Predicate\Like('venue',"%$value%");
                    }
                }
                $i++;
            }
            $select->where(array(
                new \Zend\Db\Sql\Predicate\PredicateSet($predicates, \Zend\Db\Sql\Predicate\PredicateSet::COMBINED_BY_OR)
            ));
        }
        if ($searchKey) {
            $where = new \Zend\Db\Sql\Where();
            $where->like('title', '%' . $searchKey . '%');
            $select->where($where);
        }
        if ($remdomone) {
            //$select->where(array('op.status' => '1'))->order(new Expression("RAND()"))->limit(1);
            $select->where(array('op.status' => '1'))->order(new Expression("Ol.closesetid")); 
        }
        $resultSet = array();
        //echo $sql->getSqlstringForSqlObject($select);//die;
        // echo $sql->prepareStatementForSqlObject($select);die;
        $results = $sql->prepareStatementForSqlObject($select)->execute();
        $resultSet = new \Zend\Db\ResultSet\ResultSet();
        $resultSet->initialize($results);
        $resultSet = $resultSet->toArray();
        return $resultSet;
    }
    
    public function saveOutfitToBought($iOutfitID, $iUserID)
    {
    
        $oOutfitMapping = $this->getServiceLocator()->get('OutfitsFeedsMappingTable');
        $oArticleCloset = $this->getServiceLocator()->get('ArticleBoughtTable');
        
        $aOutfitArticles = $oOutfitMapping->getOutfitsMapping(array('outfit_id' => $iOutfitID), true);
    
        foreach ($aOutfitArticles as $articles) {
            $ArticleData = $oArticleCloset->getArticleLikes($articles['id'], $iUserID);
            if( !isset($ArticleData->articlebuyid) ) {
                $oArticleCloset->saveArticleLiekes( array('articleId'=>$articles['id'], 'userId'=>$iUserID) );
            }
        }
        $aOutfitData = $this->getArticleLikes($iOutfitID, $iUserID);
        if( !isset($aOutfitData->closesetid) ) {
            $this->saveArticleLiekes( array('articleId'=>$iOutfitID, 'userId'=>$iUserID) );
        }
        
        $oOutfitCloseset = $this->getServiceLocator()->get('OutfitClosesetTable');
        $oOutfitCloseset->unSaveEntireOutfit($iOutfitID, $iUserID);
    }    
    
    public function unBoughtThisOutfit($iOutfitID, $iUserID)
    {
        $oSql = new Sql($this->getDBAdapter());
        
        $oOutfitMapping = $this->getServiceLocator()->get('OutfitsFeedsMappingTable');
        $aOutfitArticles = $oOutfitMapping->getOutfitsMapping(array('outfit_id' => $iOutfitID), true);
        
        foreach ($aOutfitArticles as $articles) {
            $delete = new Delete();
            $delete->from('ArticleBought')->where("feeddataid = '{$articles['id']}' and userId = {$iUserID}");
            //echo $oSql->getSqlStringForSqlObject($delete);
            $oSql->prepareStatementForSqlObject($delete)->execute();
        }
        
        $delete = new Delete();
        $delete->from($this->table)->where("outfeedid = '{$iOutfitID}' and userId = {$iUserID}");
        //echo $oSql->getSqlStringForSqlObject($delete);
        $oSql->prepareStatementForSqlObject($delete)->execute();
    }
    
    

}
