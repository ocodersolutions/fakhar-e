<?php
namespace Application\Model;

use Application\Model\BasicModelAdapter;

class Users extends BasicModelAdapter
{

    public function __construct()
    {
    }

    public function getUserByEmail($email)
    {
        //use the Table Gateway to find the row that
        //the id represents
        $result = $this->_db_table->find($email);

        //if not found, throw an exception
        if( count($result) == 0 ) {
            throw new Exception('User not found');
        }

        //if found, get the result, and map it to the
        //corresponding Data Object
        $row = $result->current();
        $user_object = new Application_Model_User($row);

        //return the user object
        return $user_object;
    }

}
