<?php

namespace Application\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Delete;
use Zend\Db\Sql\Update;
use Zend\Db\Sql\Select;
use \Zend\Db\Sql\Where;
use Zend\Db\Sql\Expression;
use Zend\Paginator\Paginator;
use Zend\Db\ResultSet\ResultSet;
use Zend\Paginator\Adapter\Iterator as paginatorIterator;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Db\TableGateway\TableGateway;

class OutfitsProductsTable extends BasicTableAdapter {

    protected $tableGateway;
    
    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
    
    }
        
    public function getAllOutfits()
    {
        $sql = new Sql($this->getServiceLocator()->get('db'));
        $select = $sql->select(array('op' => 'outfits_products'))->where(1);
        $results = $sql->prepareStatementForSqlObject($select)->execute();
        $resultSet = new \Zend\Db\ResultSet\ResultSet();
        $resultSet->initialize($results);
        return $resultSet->toArray();   

    }

    public function getOutfitsProducts($conditions = array(), $searchKey = null, $remdomone = null, $filterarg = null, $additionalParams=array()) {
        $sql = new Sql($this->getServiceLocator()->get('db'));
        $select = $sql->select(array('op' => 'outfits_products'));
        $select->join(array("u" => 'Users'), new Expression("(op.user_id = u.userId)"), array('user_prp_pic', 'firstName', 'lastName'), 'left');
        $select->columns( array('outfit_id', 'user_id', 'title', 'description', 'start_date', 'end_date', 'venue', 'sub_venues', 'season', 'serviceable_area', 'status', 'mobile_certified_only', 'outfitLikesCount'=>new Expression('(select count(1) from OutfitCloseset o where op.outfit_id = o.outfeedid)')) );
        foreach ($conditions as $colom => $value) {
            $select->where(array('op.' . $colom => $value));
        }
        if (isset($filterarg) && !empty($filterarg)) {
            $i = 0;
            foreach ($filterarg as $value) {	 
				if($value == 'Night Out') {
                      $value = 'NightOut'; 
                }

                if ($i == 0) {
                    $select->where->AND->like('venue', '%' . $value . '%');
                } else {
                    if (isset($value) && !empty($value)) {
                        $select->where->OR->like('venue', '%' . $value . '%');
                    }
                }
                $i++;
            }
        }
        if ($searchKey) {
            $arr = explode(':', $searchKey);
            if( is_array($arr) && count($arr)>1 && trim($arr[0])=='id' ) {
                $select->where( array('outfit_id' => $arr[1]) ); 
            }
            else {
                $select->where->OR->like('title', '%' . $searchKey . '%');
                $select->where->OR->like('outfit_id', '%' . $searchKey . '%');
            }
        }
        
        if ( isset($remdomone) && $remdomone!==false && !is_null($remdomone) ) {
            if( $remdomone === true ) $remdomone = 0;
            $select->where(array('op.status' => '1'))->offset($remdomone)->limit(3);
        }
        
        if( isset($additionalParams['outfit_id']) && $additionalParams['outfit_id'] > 0)  {
           $select->where('op.outfit_id >= '.$additionalParams['outfit_id']);
        }        
        
        $select->quantifier(new Expression('SQL_CALC_FOUND_ROWS'));
        //echo $sql->getSqlstringForSqlObject($select); die; 


        $results = $sql->prepareStatementForSqlObject($select)->execute();
        $resultSet = new \Zend\Db\ResultSet\ResultSet();
        $resultSet->initialize($results);
        $resultSetData = $resultSet->toArray();
            
             
        if ( isset($remdomone) && $remdomone!==false && !is_null($remdomone) ) {
            
            $select2 = new Select(' ');
            $select2->setSpecification(Select::SELECT, array('SELECT %1$s' => array(array(1 => '%1$s', 2 => '%1$s AS %2$s', 'combinedby' => ', '),null)));
            $select2->columns(array('total' => new Expression("FOUND_ROWS()")));
            //$sql->getSqlstringForSqlObject($select2);
            $row = $sql->prepareStatementForSqlObject($select2)->execute()->current();
            $iTotalRowsCount = $row['total'];
            
            if( $remdomone < 2 ) {
                $select->offset($iTotalRowsCount-1)->limit(1);
                $results = $sql->prepareStatementForSqlObject($select)->execute();
                $resultSet = new \Zend\Db\ResultSet\ResultSet();
                $resultSet->initialize($results);
                $resultSetTemp = $resultSet->toArray();        
    
                unset($resultSetData[2]);
                $resultSetData = array_merge($resultSetTemp, $resultSetData);
            }
            
            if( count($resultSetData) < 3 ) {
                $select->offset(0)->limit( 3-count($resultSetData) );
                $results = $sql->prepareStatementForSqlObject($select)->execute();
                $resultSet = new \Zend\Db\ResultSet\ResultSet();
                $resultSet->initialize($results);
                $resultSetTemp = $resultSet->toArray();
                
                $resultSetData = array_merge($resultSetData, $resultSetTemp);
            }        
            
            if( $remdomone == $iTotalRowsCount )  $remdomone = 0;

        }
               
        if( isset($additionalParams['returnCount']) && $additionalParams['returnCount']==true )  {
            return array($resultSetData, $iTotalRowsCount, $remdomone);
        }
        else {
            return $resultSetData;
        }
    }

    public function getOutfits($arrParam){
        try {
            if ($arrParam != null) {
                $result = $this->tableGateway->select(function (Select $select) use ($arrParam) {
                    if (isset($arrParam['cols'])) {
                        $select->columns($arrParam['cols']);
                    }
                    if (isset($arrParam['limit'])) {
                        $select->limit($arrParam['limit']);
                    }
                    if (isset($arrParam['offset'])) {
                        $select->offset($arrParam['offset']);
                    }
                    if (isset($arrParam['where'])) {
                        $select->where($arrParam['where']);
                    }
                    if (isset($arrParam['order'])) {
                        $select->order($arrParam['order']);
                    }
                    if (isset($arrParam['join'])) {
                        foreach ($arrParam['join'] as $join) {
                            $select->join($join['table'], $join['on'], $join['field'], $join['type']);
                        }
                    }
                });
            } else {
                $result = $this->tableGateway->select(); 
                $result->buffer();
            }
            return $result->toArray();
        } catch (Exception $e) {
            return null;
        }
    }

    public function getItem($where = null){
        $sql = new Sql($this->getServiceLocator()->get('db'));
        $select = $sql->select(array('op' => 'outfits_products'))
                      ->where($where);
        $resultSet = array();
        //echo $sql->getSqlstringForSqlObject($select);die();
        //echo $sql->prepareStatementForSqlObject($select); die();
        $results = $sql->prepareStatementForSqlObject($select)->execute();
        $resultSet = new \Zend\Db\ResultSet\ResultSet();
        $resultSet->initialize($results);
        $resultSet = $resultSet->current();
        return (array)$resultSet;
    }

    public function save($data = array()) {
        
        $data['venue'] = array();
        
        if( isset($data['sub_venues']) && is_array($data['sub_venues']) ) {
            
            foreach($data['sub_venues'] as $venue) {
                if(strstr($venue,'Work')) $data['venue'][] = 'Work';
                else if(strstr($venue,'NightOut')) $data['venue'][] = 'NightOut';
                else if(strstr($venue,'Casual')) $data['venue'][] = 'Casual';
            }
            $data['venue'] = implode('|',  array_unique($data['venue']));
                        
            $data['sub_venues'] = implode('|',  $data['sub_venues']);
         }        
        
        if( isset($data['season']) && is_array($data['season']) ) {
            $data['season'] = implode('|',  $data['season']);
        }        

        if( isset($data['serviceable_area']) && is_array($data['serviceable_area']) ) {
            $data['serviceable_area'] = implode('|',  $data['serviceable_area']);
            if( strstr($data['serviceable_area'], 'All')!==false ) {
                $data['serviceable_area'] = 'All';
            }
        }
               
        $id = null;
        if (!isset($data['outfit_id']) || empty($data['outfit_id'])) {
            $this->tableGateway->insert($data);
            $id = $this->tableGateway->lastInsertValue;
        } else {
            $id = $data['outfit_id'];
            unset($data['outfit_id']);
            unset($data['user_id']);
            $this->tableGateway->update($data, array('outfit_id' => $id));
        }
        return $id;
    }

    public function remove($id) {
        $this->tableGateway->delete(array('outfit_id' => (int) $id));
    }
    
    public function getUsersOutfitsProducts($conditions = array(), $searchKey = null) {
        $sql = new Sql($this->getServiceLocator()->get('db'));
        $select = $sql->select(array('op' => 'outfits_products'));
        $select->join(array('u' => 'Users'), new Expression("(op.user_id = u.userId)"), array('firstName', 'lastName'), 'left');
        $select->join(array('m' => 'outfits_feeds_mapping'), new Expression("(op.outfit_id = m.outfit_id)"), array(), 'left');
        $select->join(array('d' => 'FeedData'), new Expression("(m.feeds_uid = d.uid)"), array('all_items_avail'=>new Expression('sum(IF(item_count > 0, 0, 1))')), 'left');
        $select->group('outfit_id');
        
        //echo $sql->getSqlstringForSqlObject($select); die(); 
        
        if( is_array($conditions) ) {
            foreach ($conditions as $colom => $value) {
                $select->where(array('op.' . $colom => $value));
            }
        }
        if ($searchKey) 
        {
		  		$select->where->like('title', '%' . $searchKey . '%');
        		
        		$resultSet = array();
        		$results = $sql->prepareStatementForSqlObject($select)->execute();
        		$resultSet = new \Zend\Db\ResultSet\ResultSet();
        		$resultSet->initialize($results);
        		$resultSet = $resultSet->toArray();
        		return $resultSet;            
        }
        else 
        {
        		 // create a new result set based on the Album entity
             $resultSetPrototype = new ResultSet();
             $resultSetPrototype->setArrayObjectPrototype(new OutfitsProducts());
             // create a new pagination adapter object
             $paginatorAdapter = new DbSelect(
                 // our configured select object
                 $select,
                 // the adapter to run it against
                 $this->tableGateway->getAdapter(),
                 // the result set to hydrate
                 $resultSetPrototype
             );
             $paginator = new Paginator($paginatorAdapter);
             return $paginator;
		  }
    }  

    public function getOutfitsProductsMissing() {

        $sql = new Sql($this->getServiceLocator()->get('db'));
        $select = $sql->select(array('op' => 'outfits_products'));
        $select->join(array('u' => 'Users'), new Expression("(op.user_id = u.userId)"), array('firstName', 'lastName'), 'left');
        $select->join(array('m' => 'outfits_feeds_mapping'), new Expression("(op.outfit_id = m.outfit_id)"), array(), 'left');
        $select->join(array('d' => 'FeedData'), new Expression("(m.feeds_uid = d.uid)"), array('all_items_avail'=>new Expression('sum(IF(item_count > 0, 0, 1))')), 'left');
        $select->where(array('op.status' => 1));
        $select->group('outfit_id');
       
        
        //echo $sql->getSqlstringForSqlObject($select); die();
        

                $resultSet = array();
                $results = $sql->prepareStatementForSqlObject($select)->execute();
                $resultSet = new \Zend\Db\ResultSet\ResultSet();
                $resultSet->initialize($results);
                //$resultSet = $resultSet->toArray();
                $resultsFinal =[];
                foreach ($resultSet as $key => $value) {
                    if($value->all_items_avail > 0) {
                         $resultsFinal[] = $value;
                    }
                   
                }
                return $resultsFinal;            
        
    }    

}
