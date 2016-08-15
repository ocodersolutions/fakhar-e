<?php

namespace Application\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;

class UsersTable extends BasicTableAdapter {

    protected $table = 'Users';

    public function createUser($aData) {
        $insertData = array(
            'signup_date' => date('Y-m-d H:i:s'),
            'email' => $aData['email'],
            'password' => md5($aData['password']),
            'firstName' => $aData['firstName'],
            'lastName' => $aData['lastName'],
            'dob' => (isset($aData['year'])) ? date('Y-m-d', strtotime($aData['year'] . '-' . $aData['month'] . '-' . $aData['day'])) : $aData['dob'],
            'defaultVenue' => (isset($aData['defaultVenue'])) ? $aData['defaultVenue'] : '',
            'userType' => (isset($aData['userType'])) ? $aData['userType'] : '1'
        );

        $id = null;
        if (!isset($insertData['userId']) || empty($insertData['userId'])) {
            $id = $this->tableGateway->insert($insertData);
            return $this->tableGateway->lastInsertValue;
        } else {
            $id = $insertData['userId'];
            unset($insertData['userId']);
            unset($insertData['password']);
            $this->tableGateway->update($insertData, array('userId' => $id));
        }
        return $id;
    }
    
    public function createUserSocial($data) {
        $insertData = array(
            'signup_date' => date('Y-m-d H:i:s'),
            'facebook_id' => $data['facebook_id'],
            'email' => $data['email'],
            'password' => '',
            'firstName' => $data['firstName'],
            'lastName' => $data['lastName'],
            'defaultVenue' => (isset($data['defaultVenue'])) ? $data['defaultVenue'] : '',
            'userType' => (isset($data['userType'])) ? $data['userType'] : '1',
            //'dob' => $data['dob'],
        );

        $id = null;
        if (!isset($insertData['userId']) || empty($insertData['userId'])) {
            $id = $this->tableGateway->insert($insertData);
            return $this->tableGateway->lastInsertValue;
        } else {
            $id = $insertData['userId'];
            unset($insertData['userId']);
            unset($insertData['password']);
            $this->tableGateway->update($insertData, array('userId' => $id));
        }
        return $id;
    }

    public function isUnique($columns, $value) {
        $sql = new Sql($this->getDBAdapter());
        $value = trim($value);
        $columns = trim($columns);
        $select = new Select();
        $select->from(array('u' => 'Users'))->where("{$columns} = '{$value}'");
        $aUser = $sql->prepareStatementForSqlObject($select)->execute()->current();
        return !isset($aUser['userId']);
    }

    /* Function to return user id 
      Author  :   Punit Kumar
      Access  :   Public
      Created :   04-10-2014
      @params :   Column Name, Column value */

    public function getUserByEmail($columns, $value) {
        $sql = new Sql($this->getDBAdapter());
        $value = trim($value);
        $columns = trim($columns);
        $select = new Select();
        $select->from(array('u' => 'Users'))->where("{$columns} = '{$value}'");
        $aUser = $sql->prepareStatementForSqlObject($select)->execute()->current();
        if (!empty($aUser['userId']) && isset($aUser['userId'])) {
            return $aUser['userId'];
        } else {
            return false;
        }
    }

    public function getUserBy($columns, $value) 
    {
        $sql = new Sql($this->getDBAdapter());
        $value = trim($value);
        $columns = trim($columns);
        $select = new Select();
        $select->from(array('u' => 'Users'))->where("{$columns} = '{$value}'");
        return $sql->prepareStatementForSqlObject($select)->execute()->current();
    }
    
    public function updatePassword($aData) {
        $insertData = array(
            'password' => md5($aData['password']),
            'userId' => $aData['userId']
        );

        $id = null;
        if (!isset($insertData['userId']) || empty($insertData['userId'])) {
            $id = $this->tableGateway->insert($insertData);
            return $this->tableGateway->lastInsertValue;
        } else {
            $id = $insertData['userId'];
            unset($insertData['userId']);
            //unset($insertData['password']);
            $this->tableGateway->update($insertData, array('userId' => $id));
        }
        return $id;
    }

    public function updateMyaccount($aData, $propic = null) {

        if (isset($propic) && !empty($propic)) {
            $insertData = array(
                'userId' => $aData['userId'],
                'user_prp_pic' => $aData['user_prp_pic']
            );
        } else {
            $insertData = array(
                'firstName' => $aData['firstName'],
                'lastName' => $aData['lastName'],
                'dob' => $aData['dob'],
                'defaultVenue' => $aData['defaultVenue'],
                'userId' => $aData['userId'],
                'user_pp' => $aData['profile-pp-val'],
                'description' =>$aData['description']
            );
        }
        $id = null;
        if (!isset($insertData['userId']) || empty($insertData['userId'])) {
            $id = $this->tableGateway->insert($insertData);
            return $this->tableGateway->lastInsertValue;
        } else {
            $id = $insertData['userId'];
            unset($insertData['userId']);
            //unset($insertData['password']);
            $this->tableGateway->update($insertData, array('userId' => $id));
        }
        return $id;
    }

      public function updateMyaccountMobile($aData) {
       if (isset($propic) && !empty($propic)) {
            $insertData = array(
                'userId' => $aData['userId'],
                'user_prp_pic' => $aData['user_prp_pic']
            );
        } else {
            $insertData = array(
                'firstName' => $aData['firstName'],
                'lastName' => $aData['lastName'],
                'dob' => $aData['dob'],
                'defaultVenue' => $aData['defaultVenue'],
                'userId' => $aData['userId'],
                'user_pp' => $aData['profile-pp-val'],
            );
        }
        $id = null;
        if (!isset($insertData['userId']) || empty($insertData['userId'])) {
            $id = $this->tableGateway->insert($insertData);
            return $this->tableGateway->lastInsertValue;
        } else {
            $id = $insertData['userId'];
            unset($insertData['userId']);
            //unset($insertData['password']);
            $this->tableGateway->update($insertData, array('userId' => $id));
        }
        return $id;
    }
    
    public function updateUser($iUserId, $aData) 
    {
        $this->tableGateway->update($aData, array('userId' => $iUserId));
    }
    

    public function getUserInfoById($userId) {
        $sql = new Sql($this->getDBAdapter());
        $select = new Select();
        $select->from(array('u' => 'Users'))->where("userId = '{$userId}'");
        $aUser = $sql->prepareStatementForSqlObject($select)->execute()->current();

        if (!empty($aUser['userId']) && isset($aUser['userId'])) {
            return $aUser;
        } else {
            return false;
        }
    }

    public function getUserInfo($columns,$value){
        $sql = new Sql($this->getDBAdapter());
        $value = trim($value);
        $columns = trim($columns);
        $select = new Select();
        $select->from(array('u' => 'Users'))->where("{$columns} = '{$value}'");
        $aUser = $sql->prepareStatementForSqlObject($select)->execute()->current();
        if (!empty($aUser['userId']) && isset($aUser['userId'])) {
            return $aUser;
        } else {
            return false;
        }
    }

}
