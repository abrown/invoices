<?php

// get base template
$t = new Template( realpath('./templates/base-install.html') );

// create error html
$message = '<p>To start off, please enter some configuration information</p>';
if( $this->getCurrentStep()->getErrors() ){
    $errors = Set::flatten($this->getCurrentStep()->getErrors());
    foreach($errors as $error){
        $message .= "<p class='error'>$error</p>";
    }
}

// replace
$t->replace('title', 'Install');
$t->replace('base_url', get_base_url('invoices'));
$config = $this->getCurrentStep()->getData();
$t->replaceFromPHPFile('content', realpath('./templates/configuration.html'), array('c'=>@$_POST['config']));
$t->replace('message', $message);

// display
$t->display();