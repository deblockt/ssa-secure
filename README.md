# SSA Secure

Ssa secure is an extention of SSA[https://github.com/deblockt/ssa].

This provide a service for manager login.

## Installation

you need to add : 

``` json
	"ssa/secure": "dev-master"
```

on your composer dependencies.

## Configuration

For use this module you need add service on your serviceManager.

*config.php*
``` php
ServiceManager::getInstance()->registerAllServices(array(
    'authenticateService' => array('class' => 'ssa\secure\services\AuthenticateService')
));
```

On this exemple the service name is authenticateService, but you can choose an other service name.

After you must configure the module : 

- Add security provider : Class used for logon users
- Change tokenCryptKey : This is the key used to encrypt user id. Change default token key for better security.

*config.php*
``` php
use ssa\secure\SecureConfiguration;

// SecurityProvider is your own class who implement ISecurityProvider
SecureConfiguration::getInstance()->setSecurityProvider(new SecurityProvider());
SecureConfiguration::getInstance()->setTokenCryptKey('yourCryptKey');
```

*SecurityProvider.php*
``` php
<?php

use ssa\secure\ISecurityProvider;
use ssa\secure\SecureConfiguration;

/**
 * class used for login users
 */
class SecurityProvider implements ISecurityProvider {

	/**
	 * method used to authenticate user
	 *
	 * @param string $login the connection login
	 * @param string $mdp the connection password
	 *
	 * @return the userd id. It is is used for generate unique token for this user. null is user not exists.
	 * 		   or array if you need get specifique data on javascript, array need have a id key (it'is a unique if for identifiate user)
	 */
	public function authenticate($login, $password) {
		// here just check the user passord
		// surely that you need to do an database access
		if ($password == 'admin') {
			return array('id' => $login, 'name' => $login);
		}
		return null; // user is not recognized
	}
	
}
```

## Usage

### Javascript

You need to add authenticate service on your page :

``` html
<!-- Your service url, may be different -->
<script type="text/javascript" src="javascript.php?service=authenticateService"></script>
```

To login user, you just need to do :

``` js
authenticateService.login('username', 'password');
```

To logout user, you just need to do :

``` js
authenticateService.logout();
```

To get your authenticate info (if your authenticate method return an array) :

``` js
authenticateService.getUserInfos()();
```

#### Javascript secure listener

Three listeners are available :

- DisconnectListener : Listener called when a service return an error
- ConnectedListener : Listener called when the login is succefully do
- BadUserOrPasswordListener : Listener called when the login is badly do. Bad username or password


``` js
authenticateService.addDisconnectListener(function(){
	$('span.result').html('You are not logged');
});

// token is the crypted user id
// userInfos is array return by the authenticate method. The is index is not available on client side.
authenticateService.addConnectedListener(function(token, userInfos){
	$('span.result').html('You are logged, you can call service.');
});

authenticateService.addBadUserOrPasswordListener(function(){
	$('span.result').html('Bad loggin or password');
});
```

### php

If you want secure a service (the user need to be logged to call service), just add @Secure annotation on your service method.

``` php 

use ssa\secure\annotations\Secure;

/**
 * @author thomas
 */
class HelloWorld {
    
    /**
	 * @Secure
	 *
     * @param string $userId the userId is automatically add by secure module. It's the id returned by authenticate method 
     * @return string 
     */
    public function helloYou($userId) {
        return 'hello ' .$userId.'!!!';
    }
}
```

All Secure service can need userId parameter, this is automatically add by secure module. **Warning** : this parameter must be the last parameter or nexts parameters must have default value. 
