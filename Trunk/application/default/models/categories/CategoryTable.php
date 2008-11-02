<?php
/**
 * CategoryModel, moet zo worden gegenereerd
 *  
 * @example
 * @author Thom
 * @version 
 */
require_once 'Zend/Db/Table/Abstract.php';
class CategoryTable extends Zend_Db_Table_Abstract
{
	/**
	 * The default table name 
	 */
	protected $_name = 'category';
	protected $_nRows = 21;
	private $_pw = "dsflhjklsdhjksdrhkjldfghkdj";
	
	public function getSettings() {
		$settings = array();
		$settings['nRows'] = $this->_nRows;
		/*...*/
	}
	
	public function makelist() {
		$this->;
	}
	
	
    public function insert(array $data)
    {
    	
    	
        // key = kolom met wachtwoord
        if (empty($data['key'])) throw new CategoryTableException('Verplicht veld weggelaten');
        $data['key'] = 'AES_ENCRYPT(`'. $data['key'].'` , `'. $this->_pw . "`)";
        
        
        return parent::insert($data);
    }

    public function update(array $data, $where)
    {
        if ($data['key']) {
            $data['key'] = 'AES_ENCRYPT(`'. $data['key'].'`, `'. $this->_pw . "`)";
        }
        return parent::update($data, $where);
    }
    
    
    
    
    
	/**
	 * @throws CategoryTableException
	 */
	public function get_pw ()
	{
		throw new CategoryTableException(`You can't have my password!!!`);
	}
	/**
	 * @throws CategoryTableException
	 */
	public function set_pw ()
	{
		throw new CategoryTableException(`You can't mod my pw`);
	}

}
class CategoryTableException extends Exception {}