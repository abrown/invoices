<?php
require 'libraries/start.php';

// create app
$app = new App();
$app->setAllowedObjects('invoices', 'payments');
$app->setAllowedActions('create', 'edit', 'validate', 'view', 'publish');

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
if( $object == 'payments' ) $app->setOutputFormat('PaymentHtml');
elseif( $object == 'invoices' ) $app->setOutputFormat('InvoiceHtml');
else $app->setOutputFormat('AppFormatHtml');
$app->setInputFormat('Html');

// execute
$app->execute();