<?php 

namespace Event\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\TableGateway;
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

class EventsLogTable extends BasicTableAdapter
{
    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
    }
    
    public function fetchAll()
    {   
    	$select = $this->getSql()->select();
    	$select->where("log_signup.id != 1");
    	$select->order(array("log_signup.id"=>'DESC'));
    	//$select->join("account_did", "accounts.id = account_did.account_id",array('did','map_number'));
    	
    	$resultSet = $this->selectWith($select);
    	return $resultSet;
    }
    
    
    public function getDids($id)
    {
    	$select = $this->getSql()->select();
    	$select->where("accounts.id = $id");
    	$select->join("account_did", "accounts.id = account_did.account_id",array('did','map_number'));
    	 
    	$resultSet = $this->selectWith($select);
    	return $resultSet;
    }
    
    
    public function getEvent($id)
    {    	
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
       
        if (!$row) {
            throw new \Exception("Could not find row $id");          
        }
        return $row;
    }
    
    
    public function isExist($email)
    {
    	$rowset = $this->tableGateway->select(array('email' => $email));
    	$row = $rowset->current();
    	 
    	if (!$row) {
    		return FALSE;
    	}
    	return $row;
    }
  

    public function saveEvent($event)
    {
        $rowset = $this->tableGateway->select(array('user_ip' => $event->user_ip, 'user_id' => NULL, 'page' => $event->page));
    	$row = $rowset->current();
    	if ($row) {
    		//$this->tableGateway->delete(array('user_ip' => $event->user_ip, 'user_id' => NULL, 'page' => $event->page));
    	}
        
     	$data = array(
            'id'        => $event->id,
            'user_ip' 	=> $event->user_ip,
            'user_id' 	=> ($event->user_id)?($event->user_id):(new \Zend\Db\Sql\Expression('null')),
            'page' 		=> $event->page,
            'product_selected' 	=> $event->product_selected,
            'created' 		=> $event->created,
    	);
        
    	$id = (int)$event->id;
    	if ($id == 0) {
    		$id = $this->tableGateway->insert($data);
            return  $this->tableGateway->getLastInsertValue();
    	} else {
    		if ($this->getEvent($id)) {
    			$this->tableGateway->update($data, array('id' => $id));
    		} else {
    			throw new \Exception('Event id does not exist');
    		}
    	}		
       
    }
    
    public function updateUserLog($user_ip, $user_id)
    {
        $rowset = $this->tableGateway->select(array('user_ip' => $user_ip));
    	$row = $rowset->current();
    	if ($row) {
            $data['user_id'] = $user_id;
    		if($this->tableGateway->update($data, array('user_ip' => $user_ip)))
            {
                return true;                
            }
            else
            {
                return false;
            }
    	}
    }

    public function deleteEvent($id)
    {
        $this->tableGateway->delete(array('id' => $id));
    }
    
}