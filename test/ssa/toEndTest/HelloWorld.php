<?php

namespace ssa\secure\toEndTest;

use ssa\runner\resolver\Pojo;
use ssa\runner\converter\annotations\Encoder;
use ssa\secure\annotations\Secure;

/**
 * Description of HelloWorld
 *
 * @author thomas
 */
class HelloWorld {
    
    /**
	 * @Secure
	 *
     * @param string $yourName
     * @return string 
     */
    public function helloYou($userId) {
        return 'hello ' .$userId.'!!!';
    }
    
    /**
     * @Encoder("\ssa\runner\converter\FileEncoder")
     * 
     * @param file $file1 the file
     */
    public function getFileContent($file1) {
        return $file1;
    }
    
    /**
     * 
     * @param \ssa\runner\resolver\Pojo $pojo
     * @param array(\ssa\runner\resolver\Pojo) $pojos
     */
    public function returnPojo(Pojo $pojo, array $pojos) {
        return array($pojo, $pojos);
    }
}
