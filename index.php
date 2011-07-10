<?php
require 'libraries/start.php';

// configuration
Configuration::setPath('settings/configuration.php');
if( !is_valid(Configuration::getInstance()) ){
    // start install process
    Http::redirect('settings/configure.php');
    exit();
}

// main application
