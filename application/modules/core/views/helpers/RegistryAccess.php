<?php
/**
 * @author	2012-2013 Nihki Prihadi
 * @version $Id: RegistryAccess.php 1 2012-09-04 13:23Z $
 */

class Pandamp_Controller_Action_Helper_RegistryAccess extends Zend_Controller_Action_Helper_Abstract 
{
	/**
	 * Auth object
	 * @var Zend_Auth
	 */
	protected $_auth;

	public function __construct()
	{
		$auth = Zend_Auth::getInstance();
		
		$this->_auth = $auth;
	}
	
	public function preDispatch()
	{
		$actionController = $this->getActionController();
		
		$actionController->view->isLoggedIn 	= $this->isLoggedIn();
		$actionController->view->getIdentity 	= $this->getIdentity();
	}
	
	/**
	 * Check isLoggedIn
	 *
	 * @return boolean
	 */
	public function isLoggedIn()
	{
		if (!$this->_auth->hasIdentity()) {
			return false;
		}
		## Check if there is a cookie Set
		if (isset($_COOKIE['userid'])) {
			if ($_COOKIE["userid"] != "") {
				$values = array();
				$values['user_id'] = $_COOKIE["userid"];
				$values['ucode'] = $_COOKIE["ucode"];
				
				$this->_processCookieLogin($values);
			}
		}
		return true;
	}
	
	protected function _getCookieAuthAdapter() {
		$config = Pandamp_Config::getConfig();
		$dbAdapter = Zend_Db::factory($config->db->adapter, $config->db->master->server1->toArray());
		
		//$dbAdapter = Zend_Db_Table::getDefaultAdapter();
		
		$authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);

		$authAdapter->setTableName('login_cookies')
					->setIdentityColumn('user_id')
					->setCredentialColumn('ucode');

		return $authAdapter;
	}
	
	protected function _processCookieLogin($values) {
		// Get our authentication adapter and check credentials
		$adapter = $this->_getCookieAuthAdapter();
		$adapter->setIdentity($values['user_id']);
		$adapter->setCredential($values['ucode']);

		$result = $this->_auth->authenticate($adapter);
		if ($result->isValid()) {
			$user = $adapter->getResultRowObject();
			$this->_auth->getStorage()->write($user);
			return true;
		}
		return false;
	}
	
	public function getIdentity()
	{
		if (!$this->isLoggedIn()) return;
		
		if (isset($_COOKIE['userid'])) {
			if ($_COOKIE["userid"] != "") {
				$conn = Pandamp_Db_Connection::factory()->getMasterConnection();
				$userDb = Pandamp_Model_Dao_Factory::getInstance()->setModule('core')->getUserDao();
				$userDb->setDbConnection($conn);
				
				$user = $userDb->getById($_COOKIE["userid"]);
				
				return $user;
			}
		}
		
		return $this->_auth->getIdentity();
	}
	
    /**
     *
     * @return Pandamp_Controller_Action_Helper_RegistryAccess
     */
    public function direct()
    {
        return $this;
    }
}