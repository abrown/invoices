<?php
require '../../../pocket-knife/start.php';
require '../libraries/misc.php';
Configuration::setPath('configuration.php');

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

// install SQL if necessary
if( !has_valid_tables() ){
    require ('install.php');
}

// get config
$config = Configuration::getInstance();

// templating
$t = new Template(Configuration::get('base_dir').DS.'templates'.DS.'base.html');
$t->replace('base_url', get_base_url('invoicr'));
$t->replaceFromPHPFile('content', Configuration::get('base_dir').DS.'templates'.DS.'configuration.html', array('c'=>$config));
$t->replace('message', $message);
$t->display();