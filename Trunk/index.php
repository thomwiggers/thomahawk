<?php
//set_error_handler('Thomahawk_Errorhandler'); //eerst includen
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
$auth = Zend_Auth::getInstance();
$identity = $auth->getIdentity();

//log starten
$log = new Thomahawk_Log('./', $identity);

//zend_translate
$taal = new Zend_Translate('tmx', '/lang');

$template['titel'] = $taal->_('Startpagina');

//categorien beschikbaar maken
$cat = new Zend_Config_Ini('conf/categorie.ini');

//controleer of is authed
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
$categories = $cat->getSectionName();
try {	
	unserialize(file("inc/acl.php"));
	$i = 0;
	foreach($categories as $cat_id){
		if($acl->isAllowed($acl_role, $cat_id)){
			$template['cat_i'. ++$i] = $cat_id;				//Categorie id
			$template['cat_n'.   $i] = $cat->$cat_id->name; //categorie naam
		}
	}
} catch(Zend_Exception $e){
	/**
	 * Iets met exception doen
	 */
} catch (Zend_Acl_Exception $e){
	/**
	 * Iets doen met Exception
	 */
}