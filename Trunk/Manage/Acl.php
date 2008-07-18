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
 * @throws Manage_Acl_Exception
 */
class Manage_Acl
{
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
     * @var array
     */
    private $resources;
    /**
     * String met het resultaat van Display_Acl
     *
     * @var string
     */
    private $Acl_Html_Table;
    const ROLE = 0;
    const RESOURCE = 1;
    /**
     * Constructor van class die $acl instelt
     *
     * @param Zend_Acl $acl
     * @return Manage_Acl
     */
    function Manage_Acl ($acl)
    {
        // als $acl niet bestaat, stoppen
        if (! isset($acl)) {
            throw new Manage_Acl_Exception('Failed to load ACL');
        }
        $this->acl = $acl;
    }
    /**
     * Class die acltabel in $this->Acl_Html_Table opslaat
     *
     * @param string $what wat wil je hebben?
     */
    function Display_Acl ($what = null)
    {
        if (empty($what) || ! is_string($what)) {
            $this->setRoles(); //rollen binnenhalen
            $this->setResources(); //zelfde voor resources
            $html = '';
            foreach ($this->resources as $res) {
                $html .= '<h3 class=\'aclheader\'>' . $res . '<table>' . '<tr><th>Group</th><th>Permission</th></tr>';
                foreach ($this->roles as $rol) {
                    $this->makeHtmlTableRow($res, $rol, self::ROLE);
                }
            }
        } else { //als $what NIET leeg is
            if ($this->acl->has($what)) { //is het een Resource?
                $res = $what;
                $html = '<h3 class=\'aclheader\'>' . $res . '<table>' . '<tr><th>Group</th><th>Permission</th></tr>';
                foreach ($this->roles as $rol) { //de rijen
                    $html .= $this->makeHtmlTableRow($what, $rol, self::ROLE);
                }
            } elseif ($this->acl->hasRole($what)) { //of is het een Role
                $html = '<h3 class=\'aclheader\'>' . $what . '<table>' . '<tr><th>Resource</th><th>Permission</th></tr>';
                foreach ($this->resources as $res) { //de rijen
                    $html .= $this->makeHtmlTableRow($res, $what, self::RESOURCE);
                }
            }
            $html .= '</table>';
            $this->Acl_Html_Table = $html;
        }
    }
    /**
     * Maakt tabel van Resources en Roles
     *
     * @param string $resources De resources
     * @param string $roles     De rollen
     * @param int	 $col_type	ROLE of RESOURCE
     * @return string Html Table
     */
    private function makeHtmlTableRow ($res, $rol, $col_type)
    {
        if ($col_type === self::RESOURCE) {
            $col_name = $res;
        } elseif ($col_type === self::ROLE) {
            $col_name = $rol;
        } else {
            throw new Manage_Acl_Exception('false constant used: ' . $col_type);
        }
        $html = '<tr><td>' . $col_name . '</td><td><label for="' . $rol . '_' . $res .
        '"><input type="radio" name="' . $rol . '_' . $res . '"' . 'value="allow" checked="' .
        ($this->acl->isAllowed($rol, $res) ? 'checked' : '') .'" />Allowed</label>' . '<label for="' . $rol .
        '_' . $res . '"><input type="radio" name="' . $rol . '_' . $res . '" value="deny" checked="' .
        ($this->acl->isAllowed($rol, $res) ? '' : 'checked') . '" />Denied</label></td></tr>';
        return $html;
    }
    /**
     * Deze functie zet de rollen in $roles als een array
     *
     */
    function setRoles ()
    {
        if (empty($this->roles)) {
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
    }
    function setResources ()
    {
        if (empty($this->resources)) {
            require_once '../conf/resources.php';
            $this->resources = $resources;
        }
    }
    function setDb ()
    {
        if (empty($this->db)) {
            $ini = new Zend_Config_Ini('../conf/config.ini');
            $this->db = Zend_Db::factory($ini->type, array('host' => $ini->db->host ,
            												'username' => $ini->db->username ,
            												'password' => $ini->db->password ,
            												'dbname' => $ini->db->dbname));
            $this->prefix = $ini->db->prefix;
            unset($ini);
        }
    }
    function GetAcl_Html_Table ()
    {
        return $this->Acl_Html_Table;
    }
    function EchoAcl_Html_Table ()
    {
        echo $this->Acl_Html_Table;
    }
}
class Manage_Acl_Exception extends Exception
{}
