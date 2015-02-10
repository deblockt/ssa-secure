<?php
namespace ssa\secure\service;

use ssa\secure\SecureConfiguration;
use ssa\secure\TokenProvider;

/**
 * Service use for authenticate user
 *
 * @author thomas
 */
class AuthenticateService {
	
	/**
	 * service use to login someone
	 */
	public function login($login, $password) {
		$securityProvider = SecureConfiguration::getInstance()->getSecurityProvider();
		// security provider is mandatory
		if ($securityProvider == null) {
			throw new BadConfigurationException('security provider is mandatory');
		}
		
		// authenticate the user with the specifique method
		$authenticateResult = $securityProvider->authenticate($login, $password);
		if ($authenticateResult == null) {
			throw new UserNotExistsException();
		}
		
		if ($this->is_session_started() === FALSE) {
			session_start();
		}
		
		$provider = new TokenProvider();
		$token = $provider->generateToken($authenticateResult);
		
		$return = array('logged' => true);
		if ($secureInstance->getSecureMode() === SecureConfiguration::MODE_SESSION) {
			$_SESSION[SecureConfiguration::$tokenName] = $token;
		} else {
			$return[SecureConfiguration::$tokenName] = $token;
		}
		
		return $return;
	}
	
	
	/**
     * helper function
 	 * @return bool
	 */
	private function is_session_started() {
		if ( php_sapi_name() !== 'cli' ) {
			if ( version_compare(phpversion(), '5.4.0', '>=') ) {
				return session_status() === PHP_SESSION_ACTIVE ? TRUE : FALSE;
			} else {
				return session_id() === '' ? FALSE : TRUE;
			}
		}
		return FALSE;
	}
}