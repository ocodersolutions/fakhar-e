<?php
namespace Application\Model;

use Zend\Authentication\Storage;

class MyAuthStorage extends Storage\Session
{
	public function setRememberMe($rememberMe = 0, $time = 1209600)
	{
		if ($rememberMe == 1) {
			$this->session->getManager()->rememberMe($time);
		}
	}
	 
	public function forgetMe()
	{
		$this->session->getManager()->forgetMe();
	}

	public function getSession()
	{
		return $this->session;
	}
	
	public function set($key, $val)
	{
		$sessionData = $this->read();
		$sessionData->{$key} = $val;
		$this->write($sessionData);
	}
		
	public function get($key)
	{
		return isset($this->read()->{$key}) ? $this->read()->{$key} : null; 
	}		
}