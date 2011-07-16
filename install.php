<?php
require 'libraries/start.php';
$p = new Process();

// step 1: create config
$p->addStep(1, 'install/config.php', 'install/config-write.php', array(
    //array('config.base_url', Validation::NOT_EMPTY, 'Base URL must not be empty'),
    //array('config.base_url', Validation::URL, 'Base URL must be a valid URL'),
    array('config.db.host', Validation::NOT_EMPTY, 'Database Host must not be empty'),
    array('config.db.host', Validation::STRING, 'Database Host must be a string'),
    array('config.db.name', Validation::NOT_EMPTY, 'Database Name must not be empty'),
    array('config.db.name', Validation::STRING, 'Database Name must be a string'),
    array('config.db.username', Validation::NOT_EMPTY, 'Database Username must not be empty'),
    array('config.db.username', Validation::STRING, 'Database Username must be a string'),
    array('config.db.password', Validation::STRING, 'Database Password must be a string'),
    array('config.user.name', Validation::NOT_EMPTY, 'User Name must not be empty'),
    array('config.user.name', Validation::STRING, 'User Name must be a string'),
    array('config.user.email', Validation::NOT_EMPTY, 'E-mail must not be empty'),
    array('config.user.email', Validation::STRING, 'E-mail must be a string'),
    array('config.user.email', Validation::EMAIL, 'E-mail must be a valid e-mail address'),
    array('config.user.address1', Validation::STRING, 'Address 1 must be a string'),
    array('config.user.address2', Validation::STRING, 'Address 2 must be a string'),
    array('config.user.city', Validation::STRING, 'City must be a string'),
    array('config.user.state', Validation::STRING, 'State/Province must be a string'),
    array('config.user.zip', Validation::STRING, 'Postal Code must be a string'),
    array('config.user.country', Validation::STRING, 'Country must be a string'),
    array('config.default_invoice_theme', Validation::STRING, 'Default Invoice Theme must be a string'),
    array('config.default_receipt_theme', Validation::STRING, 'Default Receipt Theme must be a string'),
    array('config.default_wage', Validation::NUMERIC, 'Default Wage must be a decimal number'),
));

// step 2: install sql
$p->addStep(2, 'install/sql.php', 'install/sql-write.php');

// step 3: finished
$p->addStep(3, 'install/finished.php', 'install/finished-redirect.php');

// execute
$p->execute();