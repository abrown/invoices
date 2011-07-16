<?php

if( !array_key_exists('config', $_POST) || !$_POST['config'] ) throw new Exception('No data POSTed', 400);

// else
$config = Http::clean($_POST['config'], 'html');
Configuration::write($config);
Configuration::reset();

// return
return true;