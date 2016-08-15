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

class ArticleLikesTable extends BasicTableAdapter {

    protected $table = 'ArticleLikes';

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

}
