<?php

/**
 *
 *
 * @author Rafael Dohms <rdohms@gmail.com>
 */

class UGD_User
{

	private $id;
	private $login;
	private $nick;
	private $name;
	private $email;
	private $url;
	private $bio;
	private $level;

	public function __construct(){

	}




	/**
	 * @return unknown
	 */
	public function getLogin() {
		return $this->login;
	}

	/**
	 * @param unknown_type $login
	 */
	public function setLogin($login) {
		$this->login = $login;
	}

	/**
	 * @return unknown
	 */
	public function getBio() {
		return $this->bio;
	}

	/**
	 * @param unknown_type $bio
	 */
	public function setBio($bio) {
		$this->bio = $bio;
	}

	/**
	 * @return unknown
	 */
	public function getEmail() {
		return $this->email;
	}

	/**
	 * @param unknown_type $email
	 */
	public function setEmail($email) {
		$this->email = $email;
	}

	/**
	 * @return unknown
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @param unknown_type $id
	 */
	public function setId($id) {
		$this->id = $id;
	}

	/**
	 * @return unknown
	 */
	public function getLevel() {
		return $this->level;
	}

	/**
	 * @param unknown_type $level
	 */
	public function setLevel($level) {
		$this->level = $level;
	}

	/**
	 * @return unknown
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @param unknown_type $name
	 */
	public function setName($name) {
		$this->name = $name;
	}

	/**
	 * @return unknown
	 */
	public function getNick() {
		return $this->nick;
	}

	/**
	 * @param unknown_type $nick
	 */
	public function setNick($nick) {
		$this->nick = $nick;
	}

	/**
	 * @return unknown
	 */
	public function getUrl() {
		return $this->url;
	}

	/**
	 * @param unknown_type $url
	 */
	public function setUrl($url) {
		$this->url = $url;
	}



}
?>