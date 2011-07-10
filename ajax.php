<?php
require 'libraries/start.php';

// get token
try{ $page = Routing::getToken('object'); }
catch(Exception $e){ Http::setCode(400); echo "NO PAGE GIVEN"; exit; }

// check if valid
$valid_pages = array('entry-edit.php');
if( !in_array($page, $valid_pages) ){ Http::setCode(400); echo "INVALID PAGE"; exit; }

// get page
$file = Configuration::get('base_dir').DS.'templates'.DS.$page;
if( !is_file($file) ){ Http::setCode(404); echo "COULD NOT FIND PAGE"; exit; }

// put page
Http::setCode(200);
echo include($file);