<?php

/**
 * Returns the base URL based on a specified token
 * @param string $token
 * @return string 
 */
function get_base_url($token){
    $url = Http::getUrl();
    $cut = stripos( $url, $token ) + strlen( $token );
    return substr($url, 0, $cut);
}

/**
 * Tests configuration array for correctness
 * @param array $config
 * @return bool 
 */
function is_valid( $config ){
    return count(find_configuration_errors($config)) > 0 ? false : true;
}

/**
 * Returns configuration errors
 * @param array $config
 * @return array 
 */
function find_configuration_errors( $config ){
    
    // create ruleset
    $v = new Validation();
    $v->addRule('base_url', Validation::NOT_EMPTY, 'Base URL must not be empty');
    $v->addRule('base_url', Validation::URL, 'Base URL must be a valid URL');
    $v->addRule('db.host', Validation::NOT_EMPTY, 'Database Host must not be empty');
    $v->addRule('db.host', Validation::STRING, 'Database Host must be a string');
    $v->addRule('db.name', Validation::NOT_EMPTY, 'Database Name must not be empty');
    $v->addRule('db.name', Validation::STRING, 'Database Name must be a string');
    $v->addRule('db.username', Validation::NOT_EMPTY, 'Database Username must not be empty');
    $v->addRule('db.username', Validation::STRING, 'Database Username must be a string');
    //$v->addRule('db.password', Validation::NOT_EMPTY, 'Database Password must not be empty');
    $v->addRule('db.password', Validation::STRING, 'Database Password must be a string');
    $v->addRule('user.name', Validation::NOT_EMPTY, 'User Name must not be empty');
    $v->addRule('user.name', Validation::STRING, 'User Name must be a string');
    $v->addRule('user.email', Validation::NOT_EMPTY, 'E-mail must not be empty');
    $v->addRule('user.email', Validation::STRING, 'E-mail must be a string');
    $v->addRule('user.email', Validation::EMAIL, 'E-mail must be a valid e-mail address');
    $v->addRule('user.address1', Validation::STRING, 'Address 1 must be a string');
    $v->addRule('user.address2', Validation::STRING, 'Address 2 must be a string');
    $v->addRule('user.city', Validation::STRING, 'City must be a string');
    $v->addRule('user.state', Validation::STRING, 'State/Province must be a string');
    $v->addRule('user.zip', Validation::STRING, 'Postal Code must be a string');
    $v->addRule('user.country', Validation::STRING, 'Country must be a string');
    $v->addRule('default_theme', Validation::STRING, 'Default Theme must be a string');
    $v->addRule('default_wage', Validation::NUMERIC, 'Default Wage must be a decimal number');

    // get errors
    $errors = $v->validateList( Set::flatten($config) );
    
    // test database connnection
    try { get_database(); }
    catch (Exception $e) { $errors[] = 'Could not connect to database with the given database information'; }
    
    // return
    return Set::flatten($errors);
}

/**
 * Gets database instance from configuration information
 * @staticvar any $instance
 * @return PDO 
 */
function get_database(){
    static $instance = null;
    if( !$instance ) {
        // get configuration
        $config = Configuration::getInstance();
        // create PDO instance
        try {
            $dsn = "mysql:dbname={$config['db']['name']};host={$config['db']['host']}";
            $instance = new PDO($dsn, $config['db']['username'], $config['db']['password']);
        } catch (PDOException $e) {
            throw new Exception($e->getMessage(), 500);
        }
    }
    return $instance;
}

/**
 * Checks database for correct tables
 */
function has_valid_tables(){
    $tables = array('invoices', 'entries', 'plugins', 'themes');
    $_tables = array();
    // query
    $db = get_database();
    // check tables
    foreach($db->query('SHOW TABLES') as $table){
        $_tables[] = $table[0];
    }
    // return
    return array_diff($tables, $_tables) ? false : true;
}