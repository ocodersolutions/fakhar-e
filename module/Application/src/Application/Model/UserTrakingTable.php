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

class UserTrakingTable extends BasicTableAdapter {

    protected $table = 'UserTraking';

    /**
     * Functin for inserting data into profile traking table
     * for trak where user click on profile page picture tab
     * 
     * @param array $data Data array contain information store into table
     * 
     * @return int 
     */
    public function saveProfileTraking($data) {
        $result = $this->tableGateway->insert($data);
        return $result;
    }

    public function getUserTrakingInfo($sessionId,$page) {
        $rowset = $this->tableGateway->select(array('session_id' => $sessionId,'page' => $page));
        $row = $rowset->current();
        return $row;
    }

    public function updateProfileTraking($data, $sessionId, $page = null) {
        if (isset($page) && !empty($page)) {
            $condintions = array('session_id' => $sessionId, 'page' => $page);
        } else {
            $condintions = array('session_id' => $sessionId);
        }
        $result = $this->tableGateway->update($data, $condintions);
    }

}
