<?php
/**
 * Beheer ACL
 */

// opstarten:
$depth = 1;
$resource = 'manage_acl';
require_once '../inc/initialisatie.php';
/**
 * class die Acl beheert
 *
 */
class Manage_Acl {
	/**
	 * Var die acl class bevat
	 * @var Zend_Acl
	 */
	private $acl;
	/**
	 * @var Zend_Db
	 */
	private $db;
	private $prefix;
	/**
	 * @var array
	 */
	private $roles;
	
	/**
	 * Constanten voor Display_Acl
	 */
	const ALL	= 0;
	const GROUP = 1;

	/**
	 * Constructor van class die $acl instelt
	 *
	 * @param Zend_Acl $acl
	 * @return Manage_Acl
	 */
	function Manage_Acl($acl){
		// als $acl niet bestaat, stoppen
		if (!isset($acl)){
			throw new Manage_Acl_Exception('Failed to load ACL');
		}
		$this->acl = $acl;
	}
		
	/**
	 * Class die acl gaat weergeven
	 *
	 * @param Array $array_what structuur: wat, zoekterm
	 */
	function Display_Acl($what = null){
		if (empty($what) || !is_string($what)){
			if (!is_array($this->roles)){
				$this->getRoles();
				
			}
		}
	}
	/**
	 * Deze functie zet de rollen in $roles als een array
	 *
	 */
	function getRoles(){
		/**
		 * Statement maken
		 * 
		 * Dit zoekt alle verschillende rollen bij elkaar
		 */
		$this->setDb;
		$select = $this->db->select();
		$select->from($this->prefix . 'users', 'role');
		$select->distinct();
		$this->roles = $this->db->fetchAll($select);
		
	}
	
	function setDb(){
		if (empty($this->db)){
			$ini = new Zend_Config_Ini('../conf/config.ini');
			$this->db = Zend_Db::factory($ini->type, array(	'host'     => $ini->db->host,
															'username' => $ini->db->username,
															'password' => $ini->db->password,
															'dbname'   => $ini->db->dbname ));
			$this->prefix = $ini->db->prefix;
			unset($ini);
		}
	}
}

class Manage_Acl_Exception extends Exception {
	
}



?>