<?php

// setup
$db = get_database();
$db->beginTransaction();

// create tables
try{
    // create invoices
    $db->exec(
        "CREATE TABLE `invoices` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `status` varchar(45) NOT NULL,
          `pdf` text,
          `created` datetime DEFAULT NULL,
          `modified` datetime DEFAULT NULL,
          `client_first_name` varchar(255) DEFAULT NULL,
          `client_last_name` varchar(255) DEFAULT NULL,
          `client_email` varchar(255) DEFAULT NULL,
          `project` varchar(255) DEFAULT NULL,
          `company` varchar(255) DEFAULT NULL,
          `description` TEXT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8"
    );
    
    // create entries
    $db->exec(
        "CREATE TABLE `entries` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `invoice_id` int(11) NOT NULL,
          `name` varchar(255) DEFAULT NULL,
          `description` text,
          `type` varchar(30) DEFAULT NULL,
          `quantity` decimal(8,2) DEFAULT NULL,
          `amount_per` decimal(8,2) DEFAULT NULL,
          `total` decimal(10,2) DEFAULT NULL,
          `billed` datetime DEFAULT NULL,
          `created` datetime DEFAULT NULL,
          `modified` datetime DEFAULT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8"
    );
    // create themes
    $db->exec(
        "CREATE TABLE IF NOT EXISTS `themes` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `name` varchar(100) NOT NULL,
          `description` text,
          `file` text,
          `created` datetime DEFAULT NULL,
          `modified` datetime DEFAULT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8"
    );
    // create plugins
    $db->exec(
        "CREATE TABLE IF NOT EXISTS `plugins` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `name` varchar(100) NOT NULL,
          `trigger` varchar(20) DEFAULT NULL,
          `file` mediumtext,
          `created` datetime DEFAULT NULL,
          `modified` datetime DEFAULT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8"
    );
}
catch(PDOException $e){
    $db->rollBack();
}

// commit
$db->commit();
pr('Database tables created');
