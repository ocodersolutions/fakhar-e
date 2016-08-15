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

class ArticleBoughtTable extends BasicTableAdapter {

    protected $table = 'ArticleBought';

    public function getArticleLikes($id, $userid) {
        $id = (int) $id;
        $rowset = $this->tableGateway->select(array('feeddataid' => $id, 'userId' => $userid));
        $row = $rowset->current();
        return $row;
    }

    public function saveArticleLiekes($articleLikes) {
        
        $oArticleCloset = $this->getServiceLocator()->get('ArticleClosesetTable');
        $aAtricleData = $oArticleCloset->getArticleLikes($articleLikes['articleId'], $articleLikes['userId']);
        
        $data = array(
            'feeddataid' => $articleLikes['articleId'],
            'userId' => $articleLikes['userId'],
            'fromSaved' => (isset($aAtricleData->closesetid) ? 1 : 0)
        );
        $result = $this->tableGateway->insert($data);
        
        
        $oArticleCloset->unLikeArticleLike($articleLikes['articleId'], $articleLikes['userId']);
        
        return $result;
    }

    public function getArticleLikesCount($id) {
        $id = (int) $id;
        $rowset = $this->tableGateway->select(array('feeddataid' => $id));
        $count = $rowset->count();
        return $count;
    }
    

    public function unBoughtArticle($iArticelId, $iUserId) {
        
        $aAtricleData = $this->getArticleLikes($iArticelId, $iUserId);
        if( isset($aAtricleData->articlebuyid) && $aAtricleData->fromSaved==1 ) {
            $oArticleCloset = $this->getServiceLocator()->get('ArticleClosesetTable');
            $oArticleCloset->saveArticleLiekes(array('articleId'=>$iArticelId, 'userId'=>$iUserId));
        }
                
        $oSql = new Sql($this->getDBAdapter());
        $delete = new Delete();
        $delete->from('ArticleBought')->where("feeddataid = '{$iArticelId}' and userId = {$iUserId}");
        $oSql->prepareStatementForSqlObject($delete)->execute();
        

        
    }    

}
