<?php
namespace ssa\secure\services;

use ssa\secure\SecureConfiguration;
use ssa\secure\TokenProvider;

use ssa\converter\annotations\AddJavascript;

/**
 * Service use for authenticate user
 * 
 * @AddJavascript("../../javascript/ssaSecureModule.js")
 * @author thomas
 */	
class AuthenticateService {
	
	/**
	 * service use to login someone
	 */
	public function login($login, $password) {
		$secureConfig = SecureConfiguration::getInstance();
		$securityProvider = $secureConfig->getSecurityProvider();
		// security provider is mandatory
		if ($securityProvider == null) {
			throw new BadConfigurationException('security provider is mandatory');
		}
		
		// authenticate the user with the specifique method
		$authenticateResult = $securityProvider->authenticate($login, $password);
		if ($authenticateResult === null || $authenticateResult === FALSE) {
			throw new UserNotExistsException();
		}
		
		$return = array('logged' => true);
		
		$userId = null;
		
		if (is_array($authenticateResult)) {
			$userId = $authenticateResult['id'];
			unset($authenticateResult['id']); // remove the id of  result
			$return['userInfos'] = $authenticateResult;
		} else {
			$userId = $authenticateResult;
		}
		
		$provider = new TokenProvider();
		$token = $provider->generateToken($userId);
		
		$return[SecureConfiguration::$tokenName] = $token;
		
		
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
