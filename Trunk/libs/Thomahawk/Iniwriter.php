<?php
class Thomahawk_Iniwriter {
	/**
	 * Zend_Config_Ini instance
	 *
	 * @var Zend_Config_Ini
	 */
	private $inicontent;
	private $inilocation;

	/**
	 * Constructor
	 *
	 * @param string $inilocation
	 * @return Thomahawk_Iniwriter
	 */
	function Thomahawk_Iniwriter(string $inilocation){
		$this->setInicontent();
		$this->setInilocation($inilocation);
	}

	function writeIni($section, $key, $value){


	}

	function getInicontent(){
		return $this->inicontent;
	}
	function setInicontent(Zend_Config_Ini $inicontent){
		$this->inicontent = $inicontent;
	}

	function getInilocation(){
		return $this->inilocation;
	}
	function setInilocation($inilocation){
		if(is_string($inilocation)){
			$this->inilocation = $inilocation;
		}else {
			throw new Thomahawk_Iniwriter_Exception('False parameter passed, must be string. Parameter: ' . $inilocation);
		}
	}

}

class Thomahawk_Iniwriter_Exception extends Exception
{}