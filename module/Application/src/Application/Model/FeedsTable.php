<?php

namespace Application\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Delete;
use Zend\Db\Sql\Select;

class FeedsTable extends BasicTableAdapter 
{
	protected $table = 'Feeds';
	
	public function setMapping( $aData )
	{
		$this->aMapping = $aData;
	}
	
	public function getFeeds()
	{
		
		$sql = new Sql($this->getDBAdapter());
		
		$select = new Select();
		$select->from(array('f' => $this->table))->where("isActive = 1");
		
		return $sql->prepareStatementForSqlObject($select)->execute();
		
	}
	
	public function getFeedByID( $iFeedID )
	{
	
	    $sql = new Sql($this->getDBAdapter());
	
	    $select = new Select();
	    $select->from(array('f' => $this->table))->where("id = {$iFeedID}");
	
	    return $sql->prepareStatementForSqlObject($select)->execute()->current();
	
	}	
}