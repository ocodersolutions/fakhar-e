<?php
namespace Application\Form;

use Zend\Form\Form;

class ProductsListFiltersForm extends Form
{
	public function __construct($oService, $aPostData = null)
	{
		
		$oFeedData = $oService->get('FeedDataTable');
		$aFeedData = $oFeedData->getAdvertiserCaegoryList();
		$aFeedDataList = array();
		$aFeedDataList['all'] = 'All';
		foreach($aFeedData as $val)
		{
			$aFeedDataList[ $val['advertisercategory'] ] = $val['advertisercategory'];
		}
				
		// we want to ignore the name passed
		parent::__construct('addInfo');
		$this->setAttribute('method', 'post');
		
		$this->add(array(
			'type' => 'Zend\Form\Element\Select',
			'name' => 'infoStatus', 
			'attributes' => array(
				'id' => 'infoStatus',
				'value' => (isset($aPostData['infoStatus']) ?	$aPostData['infoStatus'] : "")	,
			),
			'options' => array(
				'label' => 'Info has added',
				'value_options' => array( 'both'=>'Both', 'yes'=>'Yes', 'no'=>'No' ),
			)
		));
		
		$this->add(array(
			'type' => 'Zend\Form\Element\Select',
			'name' => 'category', 
			'attributes' => array(
				'id' => 'category',
				'value' => (isset($aPostData['category']) ?	$aPostData['category'] : "")	,
			),
			'options' => array(
				'label' => 'Advertiser category',
				'value_options' => $aFeedDataList,
			)
		));
		
		$this->add(array(
				'type' => 'Zend\Form\Element\Text',
				'name' => 'start_date',
				'attributes' => array(
						'id' => 'start_date',
						'value' => (isset($aPostData['start_date']) ?	$aPostData['start_date'] : "")	,
				),
				'options' => array(
						'label' => 'Start Date'
				)
		));		

		$this->add(array(
				'type' => 'Zend\Form\Element\Text',
				'name' => 'end_date',
				'attributes' => array(
						'id' => 'end_date',
						'value' => (isset($aPostData['end_date']) ?	$aPostData['end_date'] : "")	,
				),
				'options' => array(
						'label' => 'End Date'
				)
		));
				
		$this->add(array(
				'type' => 'Zend\Form\Element\Hidden',
				'name' => 'page',
				'attributes' => array(
						'id' => 'page',
						'value' => (isset($aPostData['page']) ?	$aPostData['page'] : "1")	,
				),
		));
				
		$this->add(array(
				'name' => 'submit',
				'attributes' => array(
						'type'  => 'submit',
						'value' => 'Refresh',
						'id' => 'submitbutton',
						'class'=> 'btn btn-primary'
				),
		));
		
	}
}