Simple Todo List App in PHP / MySQL / jQuery

## Installation

To Use this sample you have to:

-   Setup a VirtualHost in Apache
-   Create MySQL user, a database and two database table.
-   Defines server specific constants in a PHP configuration file.

### Setup Apache VirtualHost

Using your traditional MySQL Client or command line, enter the following scripts:

````MySQL
    CREATE USER 'stripe_user'@'%' IDENTIFIED WITH mysql_native_password;GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, FILE, INDEX, ALTER, CREATE TEMPORARY TABLES, CREATE VIEW, EVENT, TRIGGER, SHOW VIEW, CREATE ROUTINE, ALTER ROUTINE, EXECUTE ON *.* TO 'stripe_user'@'%' REQUIRE NONE WITH MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0;SET PASSWORD FOR 'stripe_user'@'%' = 'helloStripe77&%';
````

```MySQL
    CREATE TABLE `stripe`.`todolist` ( `id` BIGINT(15) NOT NULL AUTO_INCREMENT , `accountId` BIGINT(15) NOT NULL COMMENT 'Refers to the database field `account`.`id`' , `name` VARCHAR(512) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL , `status` ENUM('DONE','TODO') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'TODO' , `state` ENUM('ACTIVE','DELETE') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'ACTIVE' , `date_created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , `date_updates` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB CHARSET=utf8 COLLATE utf8_unicode_ci;
````
