Simple Todo List App for web and mobile with a simple MVC PHP/MySQL backend and
a simple jQuery WebApp frontend.

The current application can be tested here: http://stripe.robby.ai/


## 1 - Installation

To Use this app you have to:
-   Setup a VirtualHost in Apache.
-   Create MySQL user, a database and two database table.
-   Defines server specific constants in a PHP configuration file.


### 1.1 - Setup Apache VirtualHost

Using your traditional editor, add the following Apache VirtualHost to handle the
domain or subdomain where to store this application.
Note that the Rewrite module have to be initialized.

```ssh
    <VirtualHost stripe.robby.ai:*>
      DocumentRoot /home/bibi/www/stripe.robby.ai
      ServerName stripe.robby.ai
      DirectoryIndex index.php
      RewriteEngine On

      ## --- Rewite API Page ---------------------------------------------------
      RewriteCond  %{REQUEST_FILENAME} !-f
      RewriteCond  %{REQUEST_FILENAME} !-d
      RewriteCond %{REQUEST_URI} ^/api [NC]
      RewriteRule  ^/api/(.+)/(.+)\.(json|php)(.*)?$ /src/api/$1/$2.php$4 [QSA,L]

      ## --- Handle all pages in index.php -------------------------------------
      RewriteCond %{REQUEST_URI} !^/(api)/? [NC]
      RewriteCond %{REQUEST_URI} ^/(authorize|access_token) [NC]
      RewriteCond  %{REQUEST_FILENAME} !-f
      RewriteCond  %{REQUEST_FILENAME} !-d
      RewriteRule  ^(.*)$ /index.php?$1 [QSA,L]

      <Directory /home/www/stripe.robby.ai/>
        Options -ExecCGI -FollowSymLinks -Indexes -Includes    
        AllowOverride None
        Require all granted
      </Directory>

      CustomLog /home/apache2_logs/stripe.robby.ai-access_log "combined"
      ErrorLog /home/apache2_logs/php.stripe.errors.log
      LogLevel warn
    </VirtualHost>
```


### 1.2 - Setup MySQL

Using your traditional MySQL Client or command line, enter the following scripts,
to create:
-   A dedicated MySQL user allowed to access only to this application.
-   Two database tables.

'''Create the MySQL `stripe_user` User, replace the xxxxxxx by a password of your choice''':
```MySQL
    CREATE USER 'stripe_user'@'%' IDENTIFIED WITH mysql_native_password;GRANT SELECT,
    INSERT, UPDATE, DELETE, CREATE, DROP, FILE, INDEX, ALTER, CREATE TEMPORARY TABLES,
    CREATE VIEW, EVENT, TRIGGER, SHOW VIEW, CREATE ROUTINE, ALTER ROUTINE, EXECUTE ON *.*
    TO 'stripe_user'@'%' REQUIRE NONE WITH MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0
    MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0;SET
    PASSWORD FOR 'stripe_user'@'%' = 'xxxxxxxxx';
```

'''Create the MySQL `account` Table''':
```MySQL
    CREATE TABLE `account` (
      `id` bigint(15) UNSIGNED NOT NULL COMMENT 'Unique account identifier',
      `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
      `password` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
      `first_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
      `last_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
      `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

    ALTER TABLE `account`
      ADD PRIMARY KEY (`id`),
      ADD UNIQUE KEY `email` (`email`),
      ADD KEY `first_name` (`first_name`,`last_name`);

    ALTER TABLE `account`
      MODIFY `id` bigint(15) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Unique account identifier';

    ALTER TABLE `account`
      ADD CONSTRAINT `accountId` FOREIGN KEY (`id`) REFERENCES `todolist` (`accountId`) ON DELETE CASCADE ON UPDATE CASCADE;
      COMMIT;
```

'''Create the MySQL `stripe` Table''':
```MySQL
    CREATE TABLE `todolist` (
      `id` bigint(15) UNSIGNED NOT NULL COMMENT 'Unique Todo identifier',
      `accountId` bigint(15) UNSIGNED DEFAULT NULL COMMENT 'Refers to the database field `account`.`id`',
      `name` varchar(512) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Name of the Todo',
      `position` bigint(15) UNSIGNED DEFAULT NULL COMMENT 'Ordering position of the Todo in ASC (smaller on the top of the list) ',
      `status` enum('DONE','TODO') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'TODO',
      `state` enum('ACTIVE','DELETE') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'ACTIVE',
      `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
      `date_updated` timestamp NOT NULL ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

    ALTER TABLE `todolist`
      ADD PRIMARY KEY (`id`),
      ADD KEY `name` (`name`(255)),
      ADD KEY `status` (`status`),
      ADD KEY `state` (`state`),
      ADD KEY `accountId` (`accountId`);

    ALTER TABLE `todolist`
      MODIFY `id` bigint(15) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Unique Todo identifier';
      COMMIT;
```


### 1.3 - Setup MySQL

Using your traditional text editor or sorftware IDE, edit the file:
```ssh
    ./src/config/config.php
```

And replace the following lines by your settings:

```PHP
    // _______ [ START EDITABLE ] __________________________________________________
    define( 'DB_BASE', 'stripe' );
    define( 'DB_USER', 'stripe_user' );
    define( 'DB_PASS', 'xxxxxxxxx' ); // Replace this password by the one you've set.

    define( 'SUB_DOMAIN_NAME', 'stripe' ); 				// Your server subdomaine.
    define( 'DOMAIN_NAME', 		 'robby.ai' ); 			// Your server domain name.
    define( 'DEBUG_IP', 			 '89.2.69.205' ); 	// Your local debug IP addresss.
    define( 'DB_HOST_WWW', 	 	 '91.121.80.48' );  // Your PROD server IP, (www) 	> https://stripe.robby.ai
    define( 'DB_HOST_LOCAL', 	 '127.0.0.1' ); 		// Your DEV server IP,  (local) > http://localhost:8080
    //_________ [ END EDIDATE ] ____________________________________________________
```
