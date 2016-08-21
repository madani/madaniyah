<?php
class Core_Services_Auth implements Zend_Auth_Adapter_Interface
{
	/**
	 * Authenticated success
	 * Its value must be greater than 0
	 */
	const SUCCESS = 1;
	
	/**
	 * Constant define that user has not been active
	 * Its value must be smaller than 0
	 */
	const NOT_ACTIVE = -1;
	
	/**
	 * Failure due to identity not being found.
	 */
	const FAILURE_IDENTITY_NOT_FOUND = -2;
	
	const FAILURE_CREDENTIAL_INVALID = -3;
	
	private $_username;
	private $_password;
	
	public function __construct($username, $password)
	{
		$this->_username = $username;
		$this->_password = $password;
	}
	
	/**
	 * Performs an authentication attempt
	 *
	 * @throws Zend_Auth_Adapter_Exception If authentication cannot be performed
	 * @return Zend_Auth_Result
	 */
	public function authenticate()
	{
		$user = Core_Models_User::fetchOne(['user_name' => $this->_username]);
		if (null == $user) {
			return new Zend_Auth_Result(self::FAILURE_IDENTITY_NOT_FOUND, null);
		}
		
		if ($this->verifyPassword($this->_password, $user->password) == false) {
			return new Zend_Auth_Result(self::FAILURE_CREDENTIAL_INVALID, null);
		}
		
		if ($user->status != 'activated') {
			return new Zend_Auth_Result(self::NOT_ACTIVE, null);
		}
		
		return new Zend_Auth_Result(self::SUCCESS, $user);
	}
	
	/**
	 * Verify the user's password
	 *
	 * @param string $password The user's password, usually taken from the sign in form
	 * @param string $encryptedPassword The encrypted password
	 * @return bool
	 */
	public function verifyPassword($password, $encryptedPassword)
	{
		$bcrypt = new Pandamp_Crypt_Password_Bcrypt();
		return $bcrypt->verify($password, $encryptedPassword);
	}
}