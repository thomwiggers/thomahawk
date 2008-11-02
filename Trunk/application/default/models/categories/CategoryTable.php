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
	private $_pw = "dsflhjklsdhjksdrhkjldfghkdj";
	private $settings = array(
		'encrypted' => array(
							'foo',
							'bar',
							'baz'							
						)
	);
	/**
	 * @return array $settings
	 */
	public function getSettings() {

		return $this->settings;
		/*...*/
	}
	
	/**
	 * maakt een array van de tabel
	 * 
	 * @return array Tabel als array
	 */
	public function makelist($where, $offset) {
		$select = $this->select()->where(urldecode($where));
		$collist = "";
		foreach ($this->_cols as $col) {		
				if (array_search($col, $this->settings))
				{ 
					$collist .= 'AES_DECRYPT(`'.$col.'`,`'.$this->_pw.'`), ';
				}else { $collist .= $col.','; }
		}
		// laatste ", " eraf
		$collist = substr ($collist, 0, -2);
		$select->from($this->_name, $collist);
		unset($collist, $col);
		$rowset = $this->fetchAll($select);
		return $rowset->toArray();
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
        if (isset($data['key']) && !empty($data['key'])) {
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