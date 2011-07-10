<?php

// get dir
$BASE_DIR = realpath('.');
$POCKET_KNIFE_DIR = realpath('../../pocket-knife');

// get pocket knife
require $POCKET_KNIFE_DIR.DIRECTORY_SEPARATOR.'start.php';

// get configuration
Configuration::setPath($BASE_DIR.DS.'settings/configuration.php');

// get misc library
require $BASE_DIR.DS.'libraries/misc.php';

// setup configuration
Configuration::set('base_dir', $BASE_DIR);
Configuration::set('base_url', get_base_url('invoicr') ); // TODO: fix so there is only one way to get base url; currently can be set in configuration and dynamically generated
Configuration::set('includes', $BASE_DIR.DS.'classes');

// get rid of some annoying warnings
date_default_timezone_set('America/New_York');