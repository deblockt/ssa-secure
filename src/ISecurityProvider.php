<?php

namespace ssa\secure;

use ssa\secure\SecureConfiguration;

/**
 * interface for security provider
 * this interface is used for loggin users
 */
interface ISercurityProvider {

	/**
	 * method used to authenticate user
	 *
	 * @param string $login the connection login
	 * @param string $mdp the connection password
	 *
	 * @return the userd id. This is is used for generate unique token for this user. null is user not exists
	 */
	public function authenticate($login, $password);
	
	
}