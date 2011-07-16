<?php

// get base template
$t = new Template( realpath('./templates/base-install.html') );

// create error html
$content = '<p>Next we will create the SQL tables; be advised that this will
    delete any previously created tables with the same name.</p>
    <form method="POST">
        <input type="hidden" name="install" value="1"/>
        <p>
            <input type="submit" value="Create"/>
        </p>
    </form>';

// get errors 
if( $this->getCurrentStep()->getErrors() ){
    $errors = Set::flatten($this->getCurrentStep()->getErrors());
    foreach($errors as $error){
        $content = "<p class='error'>$error</p>".$content;
    }
}

// replace
$t->replace('title', 'Install');
$t->replace('base_url', get_base_url('invoices'));
$t->replace('content', $content);

// display
$t->display();