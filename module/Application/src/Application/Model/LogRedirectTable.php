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

class LogRedirectTable extends AbstractTableGateway {

    protected $_tableGateway;

    public function __construct(TableGateway $tableGateway) {
        $this->_tableGateway = $tableGateway;
    }

    public function saveItem($arrParam = null) {
      
        try {
            if (!isset($arrParam['id'])) {
   
                $this->_tableGateway->insert($arrParam);

                return $this->_tableGateway->lastInsertValue;
            } else {
                $this->_tableGateway->update($arrParam, array('id' => $arrParam['id']));
                return $arrParam['id'];
            }
        } catch (Exception $e) {
            return null;
        }
    }


}
