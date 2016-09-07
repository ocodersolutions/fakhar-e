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

class ArticleClosesetTable extends BasicTableAdapter {

    protected $table = 'ArticleCloseset';

    public function getArticleLikes($id, $userid) {
        $id = (int) $id;
        $rowset = $this->tableGateway->select(array('feeddataid' => $id, 'userId' => $userid));
        $row = $rowset->current();
        return $row;
    }

    public function saveArticleLiekes($articleLikes) {
        $data = array(
            'feeddataid' => $articleLikes['articleId'],
            'userId' => $articleLikes['userId'],
        );
        $result = $this->tableGateway->insert($data);
        return $result;
    }

    public function getArticleLikesCount($id) {
        $id = (int) $id;
        $rowset = $this->tableGateway->select(array('feeddataid' => $id));
        $count = $rowset->count();
        return $count;
    }
    public function getAlertArticles($email) {
        // $userid = (int) $userid;
        $sql = new Sql($this->getServiceLocator()->get('db'));
        $select = $sql->select(array('ac' => 'articlealert'));
        $select->where(array('ac.email' => $email));

        $resultSet = array();
        $results = $sql->prepareStatementForSqlObject($select)->execute();
        $resultSet = new \Zend\Db\ResultSet\ResultSet();
        $resultSet->initialize($results);
        $resultSet = $resultSet->toArray();
        return $resultSet;

    }
    public function getLikedArticles($userid) {
        $userid = (int) $userid;
        $sql = new Sql($this->getServiceLocator()->get('db'));
        $select = $sql->select(array('ac' => 'ArticleCloseset'));
        $select->where(array('ac.userId' => $userid));

        $resultSet = array();
        $results = $sql->prepareStatementForSqlObject($select)->execute();
        $resultSet = new \Zend\Db\ResultSet\ResultSet();
        $resultSet->initialize($results);
        $resultSet = $resultSet->toArray();
        return $resultSet;

    }
    

    public function unLikeArticleLike($id, $userid) {
        
        $oSql = new Sql($this->getDBAdapter());
        $delete = new Delete();
        $delete->from('ArticleCloseset')->where("feeddataid = '{$id}' and userId = {$userid}");
        //echo $oSql->getSqlStringForSqlObject($delete);
        $oSql->prepareStatementForSqlObject($delete)->execute();
    }    

}
