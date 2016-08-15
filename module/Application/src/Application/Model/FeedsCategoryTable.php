<?php

namespace Application\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Delete;
use Zend\Db\Sql\Select;

class FeedsCategoryTable extends BasicTableAdapter 
{
	protected $table = 'FeedsCategoryMapping';
	protected $cachedCategoryMapping = array();
	
	public function getCategoryMappingList( $iFeedId )
	{
		$sql = new Sql($this->getDBAdapter());
	
		$select = new Select();
		$select->from(array('t' => $this->table))->where( "t.feedId = '{$iFeedId}'" );
		$aMapping =  $sql->prepareStatementForSqlObject($select)->execute();
		$aReturnArray = array();
		foreach($aMapping as $val) {
			
			$aReturnArray[ $val['advertiserCategory'] ] = explode(',', $val['ourCategory']);
		}
		return $aReturnArray;
	}	
	
	public function getCategoryMapping( $iFeedId, $sCatagory )
	{
		if( !isset($this->cachedCategoryMapping[$iFeedId]) ) {
			$this->cachedCategoryMapping = array();
			$this->cachedCategoryMapping[$iFeedId] = $this->getCategoryMappingList( $iFeedId );
		}
		
		return (isset($this->cachedCategoryMapping[$iFeedId][$sCatagory]) && is_array($this->cachedCategoryMapping[$iFeedId][$sCatagory]) ? $this->cachedCategoryMapping[$iFeedId][$sCatagory] : array()); 
	}
	
}