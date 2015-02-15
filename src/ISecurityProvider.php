<?php

namespace ssa\secure;

use ssa\secure\SecureConfiguration;

/**
 * interface for security provider
 * this interface is used for loggin users
 */
interface ISecurityProvider {

	/**
	 * method used to authenticate user
	 *
	 * @param string $login the connection login
	 * @param string $mdp the connection password
	 *
	 * @return the userd id. It is is used for generate unique token for this user. null is user not exists.
	 * 		   or array if you need get specifique data on javascript, array need have a id key (it'is a unique if for identifiate user)
	 */
	public function authenticate($login, $password);
	
	
}