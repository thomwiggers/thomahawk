<?php
//initialiseren
require_once 'inc/initialisatie.php';

//categorien beschikbaar maken
$cat = new Zend_Config_Ini('conf/categorie.ini');
$categories = $cat->getSectionName();
try {	
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

//TEMPLATE VARS
$template['titel'] = $taal->_('Startpagina');
/**
 * $template['cat_i$n'] categorieid
 * $template['cat_n$n'] categorienaam
 * $template['error']   error
 * $template['page'] pagina waar naartoe
 */
