<?php
//accelerator aan
accelerator_set_status(true);

try {
//libs bij include path
$dir = realpath(dirname(__FILE__)); //Klopt waarschijnlijk niet
set_include_path($dir . '/libs/' . PATH_SEPARATOR . get_include_path());
}catch (Exception $e){
	die ('Zend_Framework kan niet worden toegevoegd aan include_path ' . $e->__toString());
}

//Template Array
$template = array();

require_once('Zend/Loader.php');
Zend_Loader::registerAutoload();
set_error_handler('Thomahawk_Errorhandler');

//Zend_Auth
$auth = Zend_Auth::getInstance();
$identity = $auth->getIdentity();

//$acl beschikbaar maken.
unserialize(file("inc/acl.php"));

//controleer of is authed en dan ACL role ophalen
if($identity) {
	$acl_role = $auth->getStorage()->read('ACL_role');
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