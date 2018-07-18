Simple Todo List App for web and mobile with a simple MVC PHP/MySQL backend and
a simple jQuery WebApp frontend.

This Todo app allows you to:
-   Create an account based on email/password. (login/sign-in/forgot).
-   Create todos based on a simple text.
-   Update the todos text by simply selecting the todo's line.
-   Deleting the todos.
-   Ordering the todos by drag'n dropping the line your convenient order.
-   Searching for a todo base on its name.

This program is composed of three component:
-   The core backend software in PHP, a simple MVC set of files with is configuration.
-   The HTML5/Javascript Web-App frontend with jQuery + 2 external libraries.
-   The Database in MySQL with 2 tables to store the users and the todolists.

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

```apache
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
CREATE USER 'stripe_user'@'%' IDENTIFIED WITH mysql_native_password;
GRANT SELECT,INSERT, UPDATE, DELETE, CREATE, DROP, FILE, INDEX, ALTER,
CREATE TEMPORARY TABLES,CREATE VIEW, EVENT, TRIGGER, SHOW VIEW, CREATE ROUTINE,
ALTER ROUTINE, EXECUTE ON *.* TO 'stripe_user'@'%' REQUIRE NONE WITH MAX_QUERIES_PER_HOUR 0
MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0;

SET PASSWORD FOR 'stripe_user'@'%' = 'xxxxxxxxx';
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

ALTER TABLE `todolist`
  ADD CONSTRAINT `accountId` FOREIGN KEY (`accountId`) REFERENCES `account` (`id`)
  ON DELETE CASCADE ON UPDATE CASCADE;
  COMMIT;
```


### 1.3 - PHP Configuration file

Using your traditional text editor or software IDE, edit the file:
```ssh
./src/config/constants.php
```

And replace the following lines by your settings between the lines [3 > 13].

```PHP
// _______ [ START EDITABLE ] __________________________________________________
define( 'DB_BASE', 'stripe' );
define( 'DB_USER', 'stripe_user' );
define( 'DB_PASS', 'xxxxxxxxx' ); // Replace this password by the one you've set.

define( 'SUB_DOMAIN_NAME', 'stripe' ); 	     // Your server sub-domaine.
define( 'DOMAIN_NAME', 	   'robby.ai' );     // Your server domain name.
define( 'DEBUG_IP',        '89.2.69.205' );  // Your local debug IP addresse.
define( 'DB_HOST_WWW', 	   '91.121.80.48' ); // Your PROD server IP, (www)  > https://stripe.robby.ai
define( 'DB_HOST_LOCAL',   '127.0.0.1' );    // Your DEV server IP,  (local) > http://localhost:8080
//_________ [ END EDIDATE ] ____________________________________________________
```


## 2 - Choose of technologies

### 2.1 - Problem approach

The postulate form this program is to deliver in one or two days a functional application
that can be deployed easily in any server and that can be read by any developer without
any specific framework or environment knowledge.

The only dependencies of for program are on jQuery: jQuery, jQuery-ui, jQuery.bPopup and jQuery.toggles
The server-side code don't depends on a 3rd party Framework.
The server-side code have been build as Web-Service oriented, to allows the deployment
of the proprietary API in another server.  

Both of the code on frontend and backend respect the main programing Design Paterns.
Several basic technics have been made to prevent hacking (SQL-injection, cross-domain attacks,...).

This program consist on a single page Web-App where all the interaction with the server are made through   
API calls to local Web-Services.
This approach allows to separate the backend from the frontend and makes it easier
future development or language migrations.



### 2.2 - Choose of language / Framework

The languages (PHP,JS,MySQL) have been chosen based on their popularity and open-source status,
to facilitate the development (communities, external developers).

It have been deliberately chosen not to select a PHP backend framework for this program.
This allows to reduce drastically the size of the software as to increase its readability.
Removing the use of a third-party Framework requires a deep knowledge of the MVC and other Design Paterns.

It have been deliberately chosen not to create this app in a single JS Web-App using Node.js and ReactJS,
for better readability, to simplify the deployment, for future development
(using Java for the backend, place the Web-Services in another server or behind a Load-Balancer,...).
It also reduce the number of lines of the code and the weight of the program.

It have been deliberately chosen not to use several external tools that can simplify
the development or optimize the code because it was on the purpose of this program.
-   CSS: LESS/SASS YUI Compressor.
-   JS: Google Closure.
-   PHP: Composer, Eloquent, PHPUnits, PHPDocs, PHP-CS.
-   SSH: Deployment scripts, Docker.
-   Apache: Module Memcached.
-   Git: Travis.



### 2.3 - Future implementation

#### 2.3.1 - External tools
The external tools that have been deliberately not selected at first should be used for later development:
-   CSS: LESS/SASS YUI Compressor.
-   JS: Google Closure.
-   PHP: Composer, Eloquent, PHPUnits, PHPDocs, PHP-CS.
-   SSH: Deployment scripts, Docker.
-   Apache: Module Memcached.
-   Git: Travis.

#### 2.3.2 - Full-JS WebApp
Even is it haven't been selected as an initial Framework, using a Full JS Web-App
through Node.js and ReactJS have to be considered: it have the benefits of allowing
to use the same team for the backend, frontend and mobile app development.

#### 2.3.3 - Migration from MySQL > Casandra
A large development of this Web-App should end in a volume of entries that will require
a migration of the Database from MySQL to another more robust Database (relational or not, like Casandra),
the structure of the Models of the current software make this migration very easy.

#### 2.3.4 - Migration from PHP > Java
A very large development of this Web-App should end in the migration to a Java backend.
The current development of the Web-App make this migration very easy.

#### 2.3.5 - Migration from Apache to Node.js, Gnix or Cloud Based
A large development of this Web-App should end in the migration to another type of
server of in a cloud base environment.
The current development of the Web-App make this migration very easy.

#### 2.3.6 - API under OAuth2
A good approach of a modern software is to open its code to a wider community.
For a future development the API should be ported to OAuth2 and opened to other users.
This will allows the users to interact with the software from out of the app.

#### 2.3.7 - External GitHub Repositories
To facilitate the development it can be useful to split some non-core elements
of the software in external GitHub repositories, to allows the development by
third-party or by out-source organization. Ex: View components, libraries...
