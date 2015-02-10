<?php

namespace ssa\secure\annotations;

use ssa\runner\annotations\RunnerHandler;
use ssa\ServiceMetadata;

use ssa\secure\DisconnectedException;
use ssa\secure\SecureConfiguration;

use Doctrine\Common\Annotations\Annotation;


/**
 * Description of Secure
 *
 * @Annotation
 * 
 * @author thomas
 */
class Secure extends RunnerHandler {
	
    /**
     * set state magic method for cache method
     * @param type $array
     * @return \ssa\secure\annotations\Secure
     */
    public static function __set_state($array) {
        $secure = new Secure();
        return $secure;
    }
	
	/**
	 * call before service call
	 *
	 * @param string $method the action name
	 * @param array $inputParameters service parameter, (service => the service call, service.method)
	 * @param ServiceMetadata $metaData
	 *
	 * @throw Exception if action must no be call
	 */
	public function before($method,array $inputParameters,ServiceMetadata $metaData) {
		$secureInstance = SecureConfiguration::getInstance();
		
		if ($secureInstance->getSecureMode() === SecureConfiguration::MODE_SESSION) {
			// start the session
			if ($this->is_session_started() === FALSE) {
				session_start();
			}
			
			// if session token not exists user not logon
			if (!isset($_SESSION[$this->tokenName])) {
				throw new DisconnectedException();
			}
			
		} else {
			if (!isset($inputParameters[$this->tokenName])) {
				throw new DisconnectedException();
			}
		}
	}
	
	/**
	 * call before service call
	 *
	 * @param string $method the action name
	 * @param array $inputParameters service parameter, (service => the service call, service.method)
	 * @param mixed the service result before encoding
	 * @param ServiceMetadata $metaData
	 *
	 * can return value tranformed $result, encoder is call after this method
	 */
	public function after($method,array $inputParameters, $result, ServiceMetadata $metaData) {
		
	}
	
	
	
	
	
	
	
	
	
	
    /**
     * helper function
 	 * @return bool
	 */
	function is_session_started()
	{
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
