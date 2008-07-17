<?php
try {
	if (!strstr(get_include_path(), 'Zend')){
		
		//bepalen hoe diep we moeten graven tot root
		//$depth is diepte
		$downpath = '/';
		while ($depth) {
			$downpath .= '../';
			--$depth;
		}
		echo $downpath . "\n";
		//libs bij include path
		$dir = realpath(dirname(__FILE__) . $downpath); //Klopt waarschijnlijk niet
		set_include_path($dir . DIRECTORY_SEPARATOR . 'libs' . DIRECTORY_SEPARATOR . PATH_SEPARATOR . get_include_path());
	}
}catch (Exception $e){
	die ('Zend_Framework kan niet worden toegevoegd aan include_path ' . $e->__toString());
}

//Template Array
$template = array();

require_once('Zend/Loader.php');
Zend_Loader::registerAutoload();
Zend_Loader::loadFile('Thomahawk/Errorhandler.php');
set_error_handler('Thomahawk_Errorhandler');

//Zend_Auth
$auth = Zend_Auth::getInstance();
$identity = $auth->getIdentity();

//$acl beschikbaar maken.
unserialize(file("inc/acl.php"));
$acl = Zend_Acl();
//controleer of is authed en dan ACL role ophalen
if($identity) {
	$acl_role = $auth->getStorage()->read('ACL_role');
	$acl->isAllowed($acl_role, $resource);
} else {
	/**
	 * Hier moet code zodat de  
	 * inlogpagina wordt weergeven
	 * met template
	 */
	$template['error'] = $taal->_('U bent niet ingelogd');
	$template['page']  = 'login';
}

//log starten
$log = new Thomahawk_Log('./', $identity);

//zend_translate
$taal = new Zend_Translate('tmx', '/lang');