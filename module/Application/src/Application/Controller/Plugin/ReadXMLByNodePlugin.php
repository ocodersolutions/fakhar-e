<?php

namespace Application\Controller\Plugin;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

class ReadXMLByNodePlugin extends AbstractPlugin{
	
	private $sFileName;
	private $sNode;
	private $oHandle;
	private $aBufferNodes;
	private $sRemainingContents;

	public function setFileName( $sFileName ){
		$this->sFileName = $sFileName;
		return $this;
	}
	
	public function setParentNode( $sNode ){
		$this->sNode = $sNode;
		return $this;
	}
	
	public function initialize(){
		
		$this->aBufferNodes = array();
		$this->sRemainingContents = '';
		
		$this->oHandle = fopen($this->sFileName, "r");
		return $this;
	}	
	
	public function getNode(){
		
		if( count($this->aBufferNodes) ) {
			$sXML = array_shift($this->aBufferNodes);
			$oXML = simplexml_load_string( $sXML );
			return ($oXML===false ? 'ERROR' : $oXML);
		}
		
		if(!feof( $this->oHandle )) {
			$sContents = fread($this->oHandle, 8192);
			$sContents = $this->sRemainingContents . $sContents;
			$this->sRemainingContents = '';
			preg_match_all("/\<{$this->sNode}\>.*?\<\/{$this->sNode}\>/s", $sContents, $matches);
			if( is_array($matches[0]) ) {
				$this->aBufferNodes = array_merge($this->aBufferNodes, $matches[0]);
				
				$this->sRemainingContents = substr($sContents, strrpos($sContents, "</{$this->sNode}>")+strlen("</{$this->sNode}>"));
			}
			
			if( count($this->aBufferNodes) ) {
				$sXML = array_shift($this->aBufferNodes);
				$oXML = simplexml_load_string( $sXML );
				return ($oXML===false ? 'ERROR' : $oXML);
			}			
		}
		return false;
	}
}