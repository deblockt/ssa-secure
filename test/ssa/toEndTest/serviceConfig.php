<?php

include __DIR__.'/../../../vendor/autoload.php';

use ssa\ServiceManager;
use ssa\Configuration;

Configuration::getInstance()->configure(array(
    'debug' => true,
   // 'cacheMode' => 'file',
   // 'cacheDirectory' => __DIR__.'/cache'
));
ServiceManager::getInstance()->registerAllServices(array(
    'helloWorldService' => array('class' => 'ssa\secure\toEndTest\HelloWorld'),
	'authenticateService' => array('class' => 'ssa\secure\services\AuthenticateService')
));


use ssa\secure\SecureConfiguration;
use ssa\secure\toEndTest\SecurityProvider;

SecureConfiguration::getInstance()->setSecurityProvider(new SecurityProvider());