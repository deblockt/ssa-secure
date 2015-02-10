<?php

namespace ssa\secure;

use ssa\secure\SecureConfiguration;


/**
 * Class used for generate token whith a login and a password
 *
 * @author thomas
 */
class TokenProvider  {
    /**
     * separator used for separate token's part
     */
    private $separator = '\!verifAuth!/';
    

	
    /**
     * generate a unique token for the current use logged with this login and password
     * 
     * @param string $id an id, for exemple the use id
     */
    public function generateToken($id) {
        return $this->crypte($id.$this->separator.$this->getIp());
    }
	
	/**
	 * check the token and return the id
	 *
	 * @param string the crypted token
	 *
	 * @return false for invalid token, return token id for correct token
	 */
	public function checkToken($token) {
		$decrypted = $this->decrypte($token);
		
		if (strpos($decrypted, $this->separator) !== false) {
			list($id, $ip) = explode($this->separator, $decrypted);
			if ($ip == $this->getIp()) {
				return $id;
			}
		}
		
		return false;
	}
    
	/**
	 * get the crypt key
	 */
	private function getCryptKey() {
		$config = SecureConfiguration::getInstance()->getTokenCryptKey();
		return $config ? $config : 'ssaTokenProvider';
	}
	
    private function generationCle($text) { 
        $cleDEncryptage = md5($this->getCryptKey()); 
        $cmpt=0; 
        $tmpVar = ""; 
        for ($ctr=0;$ctr<strlen($text);$ctr++) { 
            if ($cmpt==strlen($cleDEncryptage)) {
                $cmpt=0; 
            }
            $tmpVar.= substr($text,$ctr,1) ^ substr($cleDEncryptage,$cmpt,1); 
            $cmpt++; 
        } 
        return $tmpVar; 
    }

    private function crypte($text) { 
        srand((double)microtime()*1000000); 
        $cleDEncryptage = md5(rand(0,32000) ); 
        $counter=0; 
        $tmpVar = ""; 
        for ($ctr=0;$ctr<strlen($text);$ctr++) { 
            if ($counter==strlen($cleDEncryptage)) {
              $counter=0; 
            }
            $tmpVar.= substr($cleDEncryptage,$counter,1).(substr($text,$ctr,1) ^ substr($cleDEncryptage,$counter,1) ); 
            $counter++;
        } 
        return base64_encode($this->generationCle($tmpVar) );
    }
    
    private function decrypte($text) { 
        $text = $this->generationCle(base64_decode($text));
        $tmpVar = ""; 
        for ($ctr=0;$ctr<strlen($text);$ctr++) { 
            $md5 = substr($text,$ctr,1); 
            $ctr++; 
            $tmpVar.= (substr($text,$ctr,1) ^ $md5); 
        } 
        
        return $tmpVar; 
    }
  

    /**
     * get user ip
     */
    private function getIp() {        
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        
        return $_SERVER['REMOTE_ADDR'];
    }
}
