Simple Todo List App for web and mobile with a simple MVC PHP/MySQL backend and
a simple jQuery/HTML5 Web-App frontend.

This Todo app allows you to:
-   Create an account based on email/password. (login/sign-in/forgot).
-   Create todos based on a simple text.
-   Update the todos text by simply selecting the todo's line.
-   Deleting the todos.
-   Ordering the todos by drag'n dropping the line to your convenient order.
-   Searching for a todo base on its name.

This program is composed of three component:
-   A software backend core in PHP, a simple MVC+WS set of files with its configuration.
-   A HTML5/Javascript Web-App frontend with jQuery + 2 external libraries.
-   A MySQL Database with 2 tables to store the Accounts and the Todolist.

The current application can be tested here: http://stripe.robby.ai/


________________________________________________________________________________
________________________________________________________________________________

## 1 - Installation

To Use this app you have to:
-   Setup a VirtualHost in Apache.
-   Create MySQL a user, a database with its two tables.
-   Defines the server specific constants in a PHP configuration file.


### 1.1 - Setup Apache VirtualHost

Using your traditional editor, add the following Apache VirtualHost to handle the
domain or subdomain where to store this application.
Note that the Apache `Rewrite` module have to be initialized.

```apache
<VirtualHost stripe.robby.ai:*>
  DocumentRoot /home/bibi/www/stripe.robby.ai
  ServerName stripe.robby.ai
  DirectoryIndex index.php
  RewriteEngine On

  ## --- Rewite API Page ---------------------------------------------------
  RewriteCond %{REQUEST_FILENAME} !-f
  RewriteCond %{REQUEST_FILENAME} !-d
  RewriteCond %{REQUEST_URI} ^/api [NC]
  RewriteRule ^/api/(.+)/(.+)\.(json|php)(.*)?$ /src/api/$1/$2.php$4 [QSA,L]

  ## --- Handle all pages in index.php -------------------------------------
  RewriteCond %{REQUEST_URI} !^/(api)/? [NC]
  RewriteCond %{REQUEST_URI} ^/(authorize|access_token) [NC]
  RewriteCond %{REQUEST_FILENAME} !-f
  RewriteCond %{REQUEST_FILENAME} !-d
  RewriteRule ^(.*)$ /index.php?$1 [QSA,L]

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

________________________________________________________________________________


### 1.2 - Setup MySQL

Using your traditional MySQL Client or command line, enter the following scripts,
to create:
-   A dedicated `MySQL user` allowed to access only to this application.
-   A database table that store the `Accounts`.
-   A database table that store the `Todolist`.

Note that the Database structure is defined in
-   `InnoDB` to speedup the read/write.
-   The CharSet in set `utf8_unicode_ci` to allow multi-language support for latter use.
-   `KEY`, `UNIQUE KEY` and `FOREIGN KEY` are set to optimize the data integrity and the search speed.

#### 1.2.1 - Create the MySQL `stripe_user` User, replace the `xxxxxxx` by a password of your choice
```MySQL
CREATE USER 'stripe_user'@'%' IDENTIFIED WITH mysql_native_password;
GRANT SELECT,INSERT, UPDATE, DELETE, CREATE, DROP, FILE, INDEX, ALTER,
CREATE TEMPORARY TABLES,CREATE VIEW, EVENT, TRIGGER, SHOW VIEW, CREATE ROUTINE,
ALTER ROUTINE, EXECUTE ON *.* TO 'stripe_user'@'%' REQUIRE NONE WITH MAX_QUERIES_PER_HOUR 0
MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0;

SET PASSWORD FOR 'stripe_user'@'%' = 'xxxxxxxxx';
```

#### 1.2.2 - Create the MySQL `account` Table
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

#### 1.2.3 - Create the MySQL `stripe` Table
```MySQL
CREATE TABLE `todolist` (
  `id` bigint(15) UNSIGNED NOT NULL COMMENT 'Unique Todo identifier',
  `accountId` bigint(15) UNSIGNED DEFAULT NULL COMMENT 'Refers to the database field `account`.`id`',
  `name` varchar(512) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Name of the Todo',
  `position` bigint(15) UNSIGNED DEFAULT NULL COMMENT 'Ordering position of the Todo in ASC',
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

________________________________________________________________________________


### 1.3 - PHP Configuration

Place the Software code in your server.
Using your traditional text editor or software IDE, edit the file:
```ssh
./src/config/constants.php
```

#### 1.3.1 - Edit Configutaion File
Replace the following lines by your settings between the lines [3 > 13].

```PHP
// _______ [ START EDITABLE ] __________________________________________________
define('DEFAULT_CHARSET','UTF-8');
define('DB_BASE',        'stripe');
define('DB_USER',        'stripe_user');
define('DB_PASS',        'xxxxxx');       // Replace this password by the one you've set.
define('SUB_DOMAIN_NAME','stripe'); 		  // Your server sub-domaine.
define('DOMAIN_NAME', 	 'robby.ai'); 		// Your server domain name.
define('DEBUG_IP',       '89.2.69.205'); 	// Your local debug IP address.
define('DB_HOST_WWW', 	 '91.121.80.48'); // Your PROD server IP,(www)   > https://stripe.robby.ai
define('DB_HOST_LOCAL',  '127.0.0.1');    // Your DEV server IP, (local) > http://localhost:8080
define( 'ERRORS_LOG','/home/www/apache2_logs/php.stripe.errors.log'); // error log file
//_________ [ END EDIDATE ] ____________________________________________________
```

#### 1.3.2 - Setup the Error Log File
Using your traditional SSH Terminal, create and set the rights to the log file
in the place you desire in your web-server, Ex:
```ssh
$> touch /home/www/apache2_logs/php.stripe.errors.log
$> chown www-data:www-data /home/www/apache2_logs/php.stripe.errors.log
$> chmod 0775 /home/www/apache2_logs/php.stripe.errors.log
```

________________________________________________________________________________
________________________________________________________________________________


## 2 - Execution of the program

### 2.1 - Single Page Web-App
The program runs on a single page, the apache server returns all request on this file:
```sh
./index.php
```

### 2.2 - Main View
This page load the configuration files that instantiate all the program dependencies
(session, database connection, static libraries)
and then this page load the view of the Class:
```sh
./src/views/Todolist_view.php
```

### 2.3 - The Javascript Web-App
Each view's Class extends a main View class that is in charge of building the page.
Once the page is displayed the JS Web-App is instantiated and inherit of the jQuery Object.
The JS Web-App are stored in two files, one for abstract generic objects and one
for page-specific objects:
```sh
./assets/js/app.js
./assets/js/web/todolist.js
```

### 2.4 - Local API Endpoints
The interaction between the JS Web-App and the Backend software is made through
a simple local API REST/HTTP under JSON format.
The Apache server is configured to handle '''*.json''' as a '''*.php''' extension.
The API endpoint's files are making a bridge with the MVC software Controllers.
It have been intentionally chosen NOT to have a single endpoint file for the API
(with a Controller mapping array) to prevent overload on the server on a same point
and to get the code more readable and also to be able to set specific settings for
each API endpoint files (Ex: set a s specific header or a cross domain ability, handle file upload...).

All API endpoints files are stored here:  
```sh
./src/api/**/**.json
```

### 2.5 - MVC+WS PHP Backend
The PHP Backend Core have been create using a simple MVC template with 3 main classes
-   Model.php
-   View.php
-   Controller.php

That are extended by all the sub-classes.

- The Controllers receive their data from the API Web-Services
- The Models can: Create/Read/Update/Delete/Search the data with the database.
- The Views render the HTML/CSS elements.


### 2.6 - Launch the Debugger on Server-Side
Using your traditional SSH Terminal, loggin on your web-server and go to the folder
where you have stored the error log file (see section 1.3.2):
```ssh
$> cd /home/www/apache2_logs/
$> tail -f php.stripe.errors.log
```


________________________________________________________________________________
________________________________________________________________________________


## 3 - Choose of technologies

### 3.1 - Problem approach

### 3.1.1 - Postulate
The postulate of this program is to deliver in one or two days a functional application
that can be deployed easily in any server and that can be read by any developer without
any specific framework or environment knowledge.
This software must work in any environment (Mac, PC), in any device (Mobile, Desktop)
and in any browser (Chrome, Firefox, IE).

### 3.1.2 - Dependencies
The only dependencies of for program are on jQuery: jQuery, jQuery-ui, jQuery.bPopup and jQuery.toggles
The server-side code don't depends on a 3rd party Framework.
The server-side code have been build as Web-Service oriented, to allows the deployment
of the proprietary API in another server.
Only one image have been used for this Web-App to limit the page load.
All graphic rendering is processed by either CSS or SVG.  

### 3.1.3 - Design Patterns and Hacking Preventions
Both of the code on frontend and backend respect the main programing Design Patterns.
Several basic technics have been made to prevent hacking (SQL-injection, cross-domain attacks,...).

### 3.1.4 - Local API as Web-Services
This program consist on a single page Web-App where all the interaction with the server are made through   
API calls to local Web-Services.
This approach allows to separate the backend from the frontend and makes it easier
future development or language migrations.

### 3.1.5 - Create Features from the Beginning
Several features have to be created from the beginning otherwise, later, they require a
more complex development.
The development of this program have been though from the beginning as it will have
future development on it, so it have been started with:
-   `Accounts support` (sign-in/login/logout).
-   `Email support` (send an email at account creation, send if the password is forgotten).
-   `Simple API` interaction between JS Web-App and PHP Backend controller's endpoints.
-   Server-side `Error Logs` to store and debug the app from the server.
-   All the code from backend to frontend is `Object-Oriented`.
-   Create the web view as HTML and CSS `Responsive`.
-   Create a minimum Web-App JS Core with: `popup support`, global loader, popup loader content, JS cache

________________________________________________________________________________


### 3.2 - Choose of language / Framework / Server

#### 3.2.1 - `Languages` selected
The languages (PHP,JS,MySQL) have been chosen based on their popularity and their open-source status,
to facilitate the development (thanks to communities and external developers).
The two languages (PHP and Javascript) handle PHP Docs and Javascript Docs to comment
the methods.
The mains Methods on both frontend and backend have been commented to facilitate the
understanding of the program.
The PHP code have been written to work in PHP>5.5 (support PHP7 and HHVM).
The JS code have written in ES2015 because later JS is not supported yet by all the browsers
(Ex: `function() {}` should become `() => {}`).

#### 3.2.2 - Server `Apache 2`
Apache server have been selected because it was already available in production to
deploy a beta test version of the app.
Other servers like Gnix could have been used instead.

#### 3.2.3 - No `Framework` on Backend
It have been deliberately chosen NOT to select a PHP backend Framework
(like Laravel, Symphony or ZEND) for this program.
This allows to reduce drastically the size of the software as to increase its readability.
Removing the use of a third-party Framework requires a deep understanding of the MVC
and other (essentials) Design Patterns: Class/Page Mapping, Class Autoloader, Builder,
Abstract Factory, Object Pool, Facade, Singleton...

#### 3.2.4 - No `Full-JS` Web-App
It have been deliberately chosen NOT to create this app in a single JS Web-App using Node.js and ReactJS,
for better readability, to simplify the deployment, for future development
(using Java for the backend, place the Web-Services in another server or behind a Load-Balancer,...).
It also reduce the number of lines of the code and the weight of the program.

#### 3.2.5 - No `External Tools`
It have been deliberately chosen NOT to use several external tools that can simplify
the development or optimize the code because it was on the purpose of this program.
-   `CSS`:          LESS/SASS YUI Compressor.
-   `JS`:           Google Closure, JS Obfuscator, Google Page Speed.
-   `PHP`:          Synfony Composer, Eloquent, PHPUnits, PHPDocs, PHP-CS.
-   `SSH`:          Deployment scripts, Docker.
-   `Cache`:        Module Memcached or Redis.
-   `Loadind Test`: JMetter scripts.
-   `Unit Test`:    PHPUnits, CI with Travis.

________________________________________________________________________________


### 3.3 - Future Implementation

#### 3.3.1 - `External Tools`
The external tools that have been deliberately not selected at first should be used for later development:
-   `CSS`:          LESS/SASS YUI Compressor to minify and optimize the CSS rendering.
-   `JS`:           Google Closure, JS Obfuscator, Google Page Speed to minify and optimize the JS files.
-   `PHP`:          Composer, Eloquent, PHPDocs, PHP-CS to automatize dependencies and code readability.
-   `SSH`:          Deployment scripts, Docker to automatize the deployment in different environment.
-   `Cache`:        Module Memcached, Redis to take advantage of caching some data (request or views).
-   `Loadind Test`: JMetter scripts to test the quality of the code.
-   `Unit Test`:    PHPUnits, CI with Travis to test the integrity of the code.

#### 3.3.2 - `Unit Test` and Continuous Integration (`CI`)
When a program get more complex it is required industrialize the testing process
on a server-side, using a set of tools that control several part of the program:
-   `PHP` Units to control the integrity of the code.
-   `JMetter` and code optimization to test the quality of the code under load pressure.
-   `Travis` sync with Git/GitHub for Continuous Integration.
-   `Docker` to sync the deployment on several environment.

#### 3.3.3 - `Cache` / `Backup` / `Load-Balancer`
When a program get more traffic it become necessary to optimize the bandwidth and
to optimize the data delivered to the clients.
Several tools can be set up:
-   `Cache` the content to prevent multiple unnecessary Database call using Memcahed or Redis.
-   Split and `Backup` the old content in different DB servers to speedup the Database request.
-   Create `MySQL Cluster` of auto-replicated Database servers behind a Load-Balancer.

#### 3.3.4 - `Full-JS` Web-App
Even is it haven't been selected as an initial Framework, using a Full JS Web-App
through `Node.js` and `ReactJS` have to be considered: it have the benefits of allowing
to use the same team for the backend, frontend and mobile app development thanks to
`React Native` that share a lot with ReactJS

#### 3.3.5 - Migration from MySQL > `Casandra`
A large development of this Web-App should end in a volume of entries that will require
a migration of the Database from MySQL to another more robust Database (relational or not, like Casandra),
the structure of the Models of the current software make this migration very easy.

#### 3.3.6 - Migration from PHP > `Java`
A very large development of this Web-App should end in the migration to a Java backend.
The current development of the Web-App make this migration very easy.
Any further development that implies the work of different developers on the same
software require the use of a well known Framework (either in PHP or Java).

#### 3.3.7 - Migration from Apache to Node.js, Gnix or `Cloud Based` (GC, AWS, OVH...)
A large development of this Web-App should end in the migration to another type of
server or in a cloud base environment.
Specific servers can also be used for specific purpose (static server for assets content).
The current development of the Web-App make this migration very easy.

#### 3.3.8 - API under `OAuth2`
A good approach of a modern software is to open its code to a wider community.
For a future development the API should be ported to OAuth2 and opened to other users.
This will allows the users to interact with the software from out of the app.

#### 3.3.9 - External `GitHub` Repositories
To facilitate the development it can be useful to split some non-core elements
of the software in external GitHub repositories, to allows the development by
third-party or by out-source organization. Ex: View components, libraries...

#### 3.3.10 - Internationalization (`I18n`)
For future development and internationalization it could be required to translate
the Web-App into static JSON or XML files and to apply a simple function to set the translation
to the according user language, Ex:
-   ./I18n/en_US.json => `{...,'text_to_translate': "text to translate",...};`
-   ./libs/Translate.php => `function _e($t) { return $translate_[$t]; }`
-   in the Views files replace => `<?="text to translate";?>` by `<?=_e('text_to_translate');?>`

#### 3.3.11 - `SEO`
For a website to get more traffic it is required to apply some pages and scripts:
-   Create static pages with content with keywords.
-   Optimized URL with keywords.
-   Sitemaps generator and script for Google for Webmaster.


________________________________________________________________________________
________________________________________________________________________________


## 4 - Copyright & License

author @BricePissard

License
----
GNU Public License
