<?php
chdir('..');
require 'libraries/start.php';

// setup
$message = '';
$config = array();

// save input
if( array_key_exists('config', $_POST) && $_POST['config'] ){
    $config = Http::clean($_POST['config'], 'html');
    $errors = find_configuration_errors($config);
    // display errors, if any
    if( $errors ){
        foreach($errors as $error){
            $message .= "<p class='error'>$error</p>";
        }
    }
    // save, if formatted correctly
    else{
        Configuration::write($config);
        Configuration::reset();
    }
}

// get config
$config = Configuration::getInstance();

// templating
$t = new Template(Configuration::get('base_dir').DS.'templates'.DS.'base.html');
$t->replace('base_url', Configuration::get('base_url'));
$t->replaceFromPHPFile('content', Configuration::get('base_dir').DS.'templates'.DS.'configuration.html', array('c'=>$config));
$t->replace('message', $message);
$t->display();