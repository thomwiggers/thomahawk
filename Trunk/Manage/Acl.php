<?php
/**
 * Beheer ACL
 */

// opstarten:
require_once '../inc/initialisatie.php';

class Manage_Acl {
	private $acl;
	
	function Manage_Acl($acl){
		// als $acl niet bestaat, stoppen
		if (!isset($acl)){
			throw new Manage_Acl_Exception('Failed to load ACL');
		}
		$this->acl = $acl;
	}
}

class Manage_Acl_Exception extends Exception {
	
}



?>