<?php
/**
 * CategoryController
 * 
 * @author Thom Wiggers
 * @version 0.01
 */
require_once 'Zend/Controller/Action.php';
class CategoryController extends Zend_Controller_Action
{
	/**
	 * alle categorieen laten zien
	 */
	public function indexAction ()
	{
		$this->view()->names = array();
		foreach(glob("../applications/models/categories/*Table.php") as $fname)
		{
									// zoek in fname Table.php en retourneer het stuk
									// voor de needle
			$this->view()->names[] = strstr($fname, 'Table.php', TRUE);
		}
	}
	
	public function viewAction() {
		/*
		 * link: /category/view/cat/BLA
		 */
		$cat = $this->_request->getParam('cat', $this->throwException('no cat id'));
							//.php|.html | ../ | ..\
		$cat = (preg_match('/(\.php|\.html|\.\./|\.\.\\)/') ? $this->_throwException(
				$cat . ' is geen valid id', 666)
				: $cat);
		require_once 'categories/' . $cat . 'Table.php';
		
		$clsname = $cat . "Table";
		
		/**
		 * 
		 * @type CategoryTable
		 */
		$tabel = new $clsname;
		unset($clsname);
		
		$tabel->makelist($this->_request->getParam('where', 'null'),
						$this->_request->getParam('offset', 0));
		
	}
	
	private function _throwException($msg, $code = null) {
		/*
		 * codes:
		 * 
		 * 666: Mglk hack
		 */
		throw new CategoryControllerException($msg, $code);
	}
	
}

class CategoryControllerException extends Exception {}
?>

