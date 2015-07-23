<?php
/**
 * This file is part of bit/helloworld.
 */
namespace stubbles\helloworld;
use stubbles\lang\Rootpath;
require __DIR__ . '/../vendor/autoload.php';
HelloWorldApp::create(new Rootpath())->run()->send();
