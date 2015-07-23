<?php
/**
 * This file is part of bit/helloworld.
 */
namespace stubbles\helloworld;
use stubbles\lang\Rootpath;
define('START_TIME', microtime(true));
require __DIR__ . '/../vendor/autoload.php';
HelloWorldApp::create(new Rootpath())->run()->send();
