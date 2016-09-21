<?php
namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;

class VenueStyleTable extends BasicTableAdapter {
    protected $table = 'VenueStyle';
	protected $tableGateway;

    public function __construct(TableGateway $tableGateway){
         $this->tableGateway = $tableGateway;
    }

    public function insert($venueStyle){
        $result = $this->tableGateway->insert($venueStyle);
        if($result){
            $result = $this->tableGateway->lastInsertValue;
        }
        return $result;
    }
}
