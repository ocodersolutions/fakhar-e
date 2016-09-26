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

    public function AddVenue($venueStyle){
        $result = $this->tableGateway->insert($venueStyle);
        return $result;
    }
    public function delete($styleid,$venueid){
        $delete = $this->tableGateway->delete(array('style_id' => (int) $styleid,'venue_id' =>(int) $venueid));
       return $delete;
    }

}
