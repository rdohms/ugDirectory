<?php

/**
 *
 */
interface UGD_Login_Module_Interface {

	public function isAuthenticated();

	/**
	 * Retrieves a User object, creating it in the local DB or just retrieving
	 * reflects the logged in user
	 *
	 * @return User
	 */
	public function getActiveUser();
	/**
	 * Returns the active user's identifier
	 *
	 * @return integer
	 *
	 */
	public function getActiveUserID();
	public function showLoginForm();
	public function logWithCredentials($username,$passwd);
	public function logout($url);

}

?>