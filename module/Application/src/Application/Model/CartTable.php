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



class CartTable extends AbstractTableGateway {

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

    public function checkExist($arrParam =null) {
        try {
            if($arrParam != null) {
                $result = $this->_tableGateway->select(function (Select $select) use ($arrParam){
                    if(isset($arrParam['limit'])){
                        $select->limit($arrParam['limit']);
                    }
                    if(isset($arrParam['offset'])){
                        $select->offset($arrParam['offset']);
                    }
                    if(isset($arrParam['where'])){
                        $select->where($arrParam['where']);
                    }
                })->count();
            }else{
                $result = $this->_tableGateway->select()->count();
                //$result->buffer();
            }
            return $result;
        } catch (Exception $e) {
            return null;
        }
    }

    public function updateQuantity($arrParam = null){
        try {
            $this->_tableGateway->update(array('quantity' => new \Zend\Db\Sql\Expression("quantity + 1")), $arrParam['where']);
        } catch (Exception $e) {
            return null;
        }
    }

    public function getCartNumber($arrParam = null) {
        try {
            if(isset($arrParam['where'])) {
                $result = $this->_tableGateway->select(function (Select $select) use ($arrParam){

                    $select->columns(array(
                        'cartNumber'=> new \Zend\Db\Sql\Expression("SUM(quantity)")
                    ));

                    $select->where($arrParam['where']);

                    // $sql = $this->_tableGateway->getSql();
                    // echo $sql->getSqlstringForSqlObject($select); die ;
                });         
            } 

             return $result->current()->toArray()['cartNumber'];
        } catch (Exception $e) {
            return null;
        }
    }

    public function getCart($arrParam = null) {
        try {
            if ($arrParam != null) {
                $result = $this->_tableGateway->select(function (Select $select) use ($arrParam){
                    if(isset($arrParam['where'])) { 
                        $select->where($arrParam['where']);
                    }
                    if (isset($arrParam['join'])) {
                        foreach ($arrParam['join'] as $join) {
                            $select->join($join['table'], $join['on'], $join['field'], $join['type']);
                        }
                    }

                    // $sql = $this->_tableGateway->getSql();
                    // echo $sql->getSqlstringForSqlObject($select); die ;
                });

                $result->buffer();
            } else {
                $result = $this->_tableGateway->select();
                $result->buffer();
            }
            return $result;
        } catch (Exception $e) {
            return null;
        }
    }

    public function delete($arrParam){
        try {
            if(isset($arrParam['where'])) {
                $this->_tableGateway->delete($arrParam['where']);
            }
        } catch (Exception $e) {

        }
    }


}
