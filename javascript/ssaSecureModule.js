/**
 * this script is auto load by authentification Service
 * is added on service definition
 */

 // service parameter is the authenticationService
function module(service) {
	const tokenName = 'securityToken';


	var currentAuthToken = undefined;
	var isHTML5 = typeof localStorage != 'undefined';
	
	// load token
	if (isHTML5) {
		currentAuthToken = localStorage.currentAuthToken;
	} else { // load for cookies
		currentAuthToken = getCookie('currentAuthToken');
	}
		
	function getToken() {
		return currentAuthToken;
	}

	function setToken(token, userInfos) {
		currentAuthToken = token;
		// add token on memory 
		if (isHTML5) {
			if (token) {
				localStorage.currentAuthToken = token;
			} else {
				localStorage.removeItem('currentAuthToken');
			}
			
			if (userInfos) {
				localStorage.userInfos = JSON.stringify(userInfos);
			} else {
				localStorage.removeItem('userInfos');
			}
		} else {
			setCookie('currentAuthToken', token,token ? 365 : -1);
			if (userInfos) {
				setCookie('userInfos', JSON.stringify(userInfos), 365);
			} else {
				setCookie('userInfos', undefined, -1);
			}			
		}
	}	
	
	
	function getUserInfos() {
		var infos = undefined;
		
		if (isHTML5) {
			infos = localStorage.userInfos;
		} else {
			infos = getCookie('userInfos');
		}
		
		return infos ? JSON.parse(infos) : undefined;
	}

	// if mode is restfull, add token on requet
	ssa.addStartCallListener(function(data){
		if (currentAuthToken) {
			data[tokenName] = currentAuthToken;
		}
	});

	ssa.addEndCallListener(function(result){
		if (result) {
			if (result.class == "ssa\\secure\\DisconnectedException") {
				callListener(disconnectListener, this);
				return false;
			} else if (result.class == "ssa\\secure\\services\\UserNotExistsException") {
				callListener(badUserOrPasswordListener, this);
			} else if (result.logged && result.logged === true) {
				setToken(result[tokenName], result['userInfos']);
				callListener(connectedListener, this, [result[tokenName], result['userInfos']]);
			} 
		}
		return true;
	});

	var disconnectListener = [];
	var connectedListener = [];
	var badUserOrPasswordListener = [];

	/**
	 * call disconects listeners
	 * @param host is the bject use on ssa 
	 */
	var callListener = function(listeners, host, params){
		for (var i in listeners) {
			listeners[i].apply(host, params || []);
		}
	};


	/**
	 * listener call when user is disconected
	 */
	service.addDisconnectListener = function(listener){
		disconnectListener.push(listener);
	},
	/**
	 * listener call when user is successfully connected
	 */
	service.addConnectedListener = function(listener) {
		connectedListener.push(listener);
	},
	/**
	 * listener call when base user or password
	 */
	service.addBadUserOrPasswordListener = function(listener) {
		badUserOrPasswordListener.push(listener);
	},
	/** 
	 * set the current user token
	 * can be used for multiple page application
	 * by default the token is save on cookie or html 5 local storage
	 * is parameter of setToken is undefined, the user will be disconnected (restfull mode)
	 */
	service.setToken = setToken;
	
	/**
	 * logout the current user
	 */
	service.logout = setToken;
	
	/**
	 * return the current user token
	 */
	service.getToken = getToken;

	
	/**
	 * return the user infos return by authenticate methods
	 */
	service.getUserInfos = getUserInfos;

	/************************************************
	*********** ADD COOKIE SUPPORT ******************
	*************************************************/
		
	function setCookie(cname, cvalue, exdays) {
		var d = new Date();
		d.setTime(d.getTime() + (exdays*24*60*60*1000));
		var expires = "expires="+d.toUTCString();
		document.cookie = cname + "=" + cvalue + "; " + expires;
	}

	function getCookie(cname) {
		var name = cname + "=";
		var ca = document.cookie.split(';');
		for(var i=0; i<ca.length; i++) {
			var c = ca[i];
			while (c.charAt(0)==' ') c = c.substring(1);
			if (c.indexOf(name) == 0) return c.substring(name.length, c.length);
		}
		return "";
	}
}
