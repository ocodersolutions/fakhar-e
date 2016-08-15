<?php
namespace Ocoder\View\Helper;
use Zend\View\Helper\AbstractHelper;

class CmsLinkTest extends AbstractHelper 
{
	
	public function __invoke($options = null){
		
		$options['controller']	= !empty($options['controller']) ? $options['controller'] : 'index';
		$options['action']		= !empty($options['action']) ? $options['action'] : 'index';
		$options['id']			= !empty($options['id']) ? $options['id'] : null;
		
		$urlPlugin	= $this->getView()->plugin('url');
		return $urlPlugin( 'test/default', array (
				'controller' 	=> $options['controller'],
				'action' 		=> $options['action'],
				'id' 			=> $options['id'],
		));
	}
}