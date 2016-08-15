<?php
namespace Ocoder\View\Helper;
use Zend\View\Helper\AbstractHelper;

class CmsInfoPartner extends AbstractHelper {
	
	public function __invoke($logo, $linkEdit, $username,$options = null){
		if(!empty($logo)){
			$logoURL	= URL_UPLOADS . '/partners/thumb/' . $logo;
		}else{
			$logoURL	= URL_UPLOADS . '/partners/thumb/no-logo.png';
		}
		
		return sprintf('<div class="user-panel" style="text-align:left">
							<div class="pull-left image">
								<img src="%s" class="img-circle" alt="Partner Image">
							</div>
							<div class="pull-left info">
								<p><a href="%s">%s</a></p>
							</div>
						</div>', $logoURL, $linkEdit, $username);
	}
}