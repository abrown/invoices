<?php

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


// setup
$db = get_database();
$db->beginTransaction();

// create tables
try{
    // create invoices
    $db->exec("

        delimiter $$

        DROP TABLE IF EXISTS `discounts`, `entries`, `invoices`, `payments`, `plugins`, `themes`$$

        delimiter $$

        CREATE TABLE `discounts` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `invoice_id` int(11) NOT NULL,
          `type` varchar(30) DEFAULT NULL,
          `quantity` decimal(8,2) DEFAULT NULL,
          `created` datetime DEFAULT NULL,
          `modified` datetime DEFAULT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8$$

        delimiter $$

        CREATE TABLE `entries` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `invoice_id` int(11) NOT NULL,
          `name` varchar(255) DEFAULT NULL,
          `description` text,
          `quantity` decimal(8,2) DEFAULT NULL,
          `amount_per` decimal(8,2) DEFAULT NULL,
          `total` decimal(10,2) DEFAULT NULL,
          `billed` datetime DEFAULT NULL,
          `created` datetime DEFAULT NULL,
          `modified` datetime DEFAULT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8$$

        delimiter $$

        CREATE TABLE `invoices` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `status` varchar(45) NOT NULL,
          `total` decimal(10,2) DEFAULT NULL,
          `pdf` text,
          `created` datetime DEFAULT NULL,
          `modified` datetime DEFAULT NULL,
          `client_first_name` varchar(255) DEFAULT NULL,
          `client_last_name` varchar(255) DEFAULT NULL,
          `client_email` varchar(255) DEFAULT NULL,
          `project` varchar(255) DEFAULT NULL,
          `company` varchar(255) DEFAULT NULL,
          `description` text,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8$$

        delimiter $$

        CREATE TABLE `payments` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `invoice_id` int(11) NOT NULL,
          `type` varchar(30) DEFAULT NULL,
          `description` text,
          `total` decimal(8,2) DEFAULT NULL,
          `billed` datetime DEFAULT NULL,
          `created` datetime DEFAULT NULL,
          `modified` datetime DEFAULT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8$$

        delimiter $$

        CREATE TABLE `plugins` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `name` varchar(100) NOT NULL,
          `trigger` varchar(20) DEFAULT NULL,
          `file` mediumtext,
          `created` datetime DEFAULT NULL,
          `modified` datetime DEFAULT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8$$

        delimiter $$

        CREATE TABLE `themes` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `name` varchar(100) NOT NULL,
          `description` text,
          `file` text,
          `created` datetime DEFAULT NULL,
          `modified` datetime DEFAULT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8$$
    "); 
}
catch(PDOException $e){
    $db->rollBack();
}

// commit
$db->commit();