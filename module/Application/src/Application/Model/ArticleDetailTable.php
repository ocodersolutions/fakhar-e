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

class ArticleDetailTable extends BasicTableAdapter {

    protected $table = 'ArticleDetail';

    /**
     * Functin for inserting data into article datail tables
     * 
     * @param Array $data list of table feild with it's value
     * 
     * @return int Return Last inserted id of record
     */
    public function saveArticleDetailInfo($data) {
        $result = $this->tableGateway->insert($data);
        return $result;
    }
/**
 * Function for geting current article information
 * 
 * @param int $id article id
 * @param int $userid id
 * 
 * @return array object
 */
    public function getArticleDetailInfo($id, $userid) {
        $id = (int) $id;
        $rowset = $this->tableGateway->select(array('feeddataid' => $id, 'userId' => $userid));
        $row = $rowset->current();
        return $row;
    }

}
