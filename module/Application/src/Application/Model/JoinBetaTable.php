<?php

namespace Application\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Delete;
use Zend\Db\Sql\Select;

class JoinBetaTable extends BasicTableAdapter 
{
	protected $table = 'JoinBeta';
	
    public function addRecord($aData) {
        
        $insertData = array(
            'email' => $aData['joinBetaEmail'],
            //'agreeToReceive' => ((isset($aData['joinBetaCheckBox']) && $aData['joinBetaCheckBox']=='on') ? 1 : 0),
            'agreeToReceive' => $aData['agreeToReceive'],
            'joinType' => (isset($aData['joinType']) ? $aData['joinType'] : '')
        );

        $this->tableGateway->insert($insertData);
        return $this->tableGateway->lastInsertValue;;
    }

    public function updateRecord($aData) {
        if(isset($aData['where'])) {
            $this->tableGateway->update($aData['data'], $aData['where']);
            return 1;
        }
        return 0;
    }

    public function getItem($params) {
        try {
            $row = $this->tableGateway->select($params)->current();
            if (empty($row)) {
                return false;
            }
            return $row;
        } catch (Exception $e) {
            return null;
        }
    }


}