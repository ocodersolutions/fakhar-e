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
use Zend\Db\TableGateway\TableGateway;

class ConfigTable extends AbstractTableGateway {

    protected $_tableGateway;

    public function __construct(TableGateway $tableGateway) {

        $this->_tableGateway = $tableGateway;

    }
    public function test(){
        die('test');
    }
    public function getUserEmailNotification($arrParam = null){
        try {
            if ($arrParam != null) {
                $result = $this->_tableGateway->select(function (Select $select) use ($arrParam) {
                    
                    if (isset($arrParam['where'])) {
                        $select->where($arrParam['where']);
                    }

                    if (isset($arrParam['cols'])) {
                        $select->columns($arrParam['cols']);
                    }
             
                });
            }
            else {
                $result = $this->_tableGateway->select();
                $result->buffer();
            }
            return $result;
        } catch (Exception $e) {
            return null;
        }
    } 
}
