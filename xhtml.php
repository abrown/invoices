<?php
require 'libraries/start.php';

// create app
$app = new App();
$app->setAllowedObjects('invoices');
$app->setAllowedActions('create', 'edit', 'validate');

// routing
try{ $object = Routing::getToken('object'); }
catch(Exception $e){ $object = 'home'; }

// show home page
if( $object == 'home' ){
    $t = new Template('templates/base.html');
    $t->replace('base_url', Configuration::get('base_url'));
    $t->replaceFromFile('content', 'templates/home.html');
    $t->replaceFromPHPFile('invoice-list', 'templates/invoice-list.php');
    $t->display();
    exit();
}

// allowed input
$app->setOutputFormat('InvoiceHtml');
$app->setInputFormat('Html');

// execute
$app->execute();