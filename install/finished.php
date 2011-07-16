<?php

// get base template
$t = new Template( realpath('./templates/base-install.html') );

// create error html
$content = '<p>You are done!</p>
    <form method="POST">
        <input type="hidden" name="install" value="1"/>
        <p>
            <input type="submit" value="Continue to home page"/>
        </p>
    </form>';

// replace
$t->replace('title', 'Install');
$t->replace('base_url', get_base_url('invoices'));
$t->replace('content', $content);

// display
$t->display();