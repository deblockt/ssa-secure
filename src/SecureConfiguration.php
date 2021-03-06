<?php
namespace ssa\secure;



/**
 * Class used for configure secure module
 *
 * @author thomas
 */
class SecureConfiguration  {
    /**
     *
     * @var Configuration
     */
    private static $instance;

	/**
	 * this key is used for crypt connection token
	 * @var string
	 */
	private $tokenCryptKey;

	/**
	 * the security provider, used for authenticate user
	 * @var ISercurityProvider
	 */
	private $securityProvider;
	
	/**
	 * token used for parameter user token. Warning on update this var, update to on ssaSecure.js
	 */
	public static $tokenName = 'securityToken';
	
    /**
     * get the configuration manager
     *
     * @return Configuration
     */
    public static function getInstance() {
        if (self::$instance == null) {
			// create configuration singleton
            self::$instance = new SecureConfiguration();
        }
        return self::$instance;
    }

	/**
     * get the configuration manager
     *
     * @param string $tokenCryptKey the new token crypt key
     */
	public function setTokenCryptKey($tokenCryptKey) {
		$this->tokenCryptKey = $tokenCryptKey;
	}

	/**
	 * @return string the token crypt key
	 */
	public function getTokenCryptKey() {
		return $this->tokenCryptKey ;
	}

	/**
	 * Set the security provider, used for authenticate user
	 * @param ISercurityProvider $securityProvider the new securityProvider
	 */
	public function setSecurityProvider(ISecurityProvider $securityProvider) {
		$this->securityProvider = $securityProvider;
	}
	
	/**
	 * @return the security provider
	 */
	public function getSecurityProvider() {
		return$this->securityProvider;
	}
}
