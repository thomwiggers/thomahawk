<?php
/*
 * Deze pagina bevat de class voor gebruikersbeheer
 */


class Usercontrol {
	//vars
	/**
	 * @var Zend_Db
	 */
	private $db;
	private $dbresult;
	private $prefix;
	
	function Usercontrol($path_from_root){
		//verbinden met db
		$ini = new Zend_Config_Ini($path_from_root . "/config.ini");
		$this->db =  Zend_Db::factory($ini->db->type, array($ini->db->toArray()));
		$this->prefix = $ini->db->prefix;
	}
	
	function queryUser ($userid){
		$query = $this->db->select();
		$query->from($prefix.'users');
		$query->where('id = ?', $userid);
		
	}
}
?>