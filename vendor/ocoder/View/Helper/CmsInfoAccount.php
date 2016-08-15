<?php
namespace Ocoder\View\Helper;
use Zend\View\Helper\AbstractHelper;

class CmsInfoAccount extends AbstractHelper {
	
	public function __invoke($avatar, $linkEdit, $email, $fullName,$options = null){
		if(!empty($avatar)){
			$avatarURL	= URL_UPLOADS . '/accounts/thumb/' . $avatar;
		}else{
			$avatarURL	= URL_UPLOADS . '/accounts/thumb/no-avatar.png';
		}
		
		return sprintf('<div class="user-panel" style="text-align:left">
							<div class="pull-left image">
								<img src="%s" class="img-circle" alt="User Image">
							</div>
							<div class="pull-left info">
								<p><a href="%s">%s</a></p>
								<span>%s</span>
							</div>
						</div>', $avatarURL, $linkEdit, $email ,$fullName);
	}
}