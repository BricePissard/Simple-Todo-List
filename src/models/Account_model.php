<?php namespace Stripe;

if (!AUTHORIZED) {
    die("Hacking Attempt [Accounts_model] : ". $_SERVER['REMOTE_ADDR']);
}
/**
 *
 * CREATE TABLE  `stripe`.`accounts` (
 *  `id`             	 BIGINT( 15 )       UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
 *	`first_name`    	 VARCHAR( 128 )     CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
 *  `last_name`      	 VARCHAR( 128 )     CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
 * 	`password` 			   VARCHAR( 128 )   	CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
 *	`email`          	 VARCHAR( 128 )     CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
 *	`date_created`     TIMESTAMP      	  NOT NULL DEFAULT CURRENT_TIMESTAMP  COMMENT 'Format ATOM: YYYY-MM-DD HH:ii:ss',
 *	PRIMARY KEY 			  (`id`),
 *  UNIQUE KEY `email` 	(`email`),
 * ) ENGINE = INNODB CHARACTER SET utf8 COLLATE utf8_unicode_ci;
 */
final class Account_model extends \Stripe\Model implements \Stripe\iCRUDS
{
    const TABLE            = 'account';

    const PASSWORD_ENCRYPT = 'encrypt';
    const PASSWORD_DECRYPT = 'decrypt';

    public static function read($_data)
    {
        $_a = [];

        if (isset($_data[ 'id' ]) && self::_control($_data[ 'id' ]) === true) {
            self::$sql =
      " SELECT * ".
      " FROM ". DB_BASE . "." . self::TABLE .
      " WHERE id = '". intval($_data[ 'id' ]) . "' " .
             ((isset($_data[ 'email' ]) && !empty($_data[ 'email' ]) && is_string($_data[ 'email' ]) && strlen($_data[ 'email' ]) > 0) ? " AND email='" . \Strings::DBTextClean($_data[ 'email' ]) . "' " : '') .
         " LIMIT 100;";

            if (is_callable([ self::$DB, 'prepare' ]) === true) {
                try {
                    $query = self::$DB->prepare(self::$sql);

                    if (!$query) {
                        self::error(__CLASS__, __METHOD__, self::$sql, (is_callable([ $DB, 'errorInfo' ]) === true) ? $DB->errorInfo() : '');
                    }
                    if ($query->execute()) {
                        for ($i = 0 ; $row = $query->fetch() ; $i++) {
                            $_a = self::_list($_a, $row, $i);
                        }

                        // -- control account password only after checking if the email was in the DB to use the latest password decryption algo.
                        // -- the password is only encrypted in the DB, the data send throught the local API is always decrypted.
                        if (
              isset($_data[ 'email'    ]) && !empty($_data[ 'email' 		]) && is_string($_data[ 'email' 	 ]) && strlen($_data[ 'email'  	 ]) > 0 &&
              isset($_data[ 'password' ]) && !empty($_data[ 'password' ]) && is_string($_data[ 'password' ]) && strlen($_data[ 'password' ]) > 0 &&
              isset($_a) && !empty($_a) && count($_a) === 1
            ) {
                            $password_crypted = $_a[ 0 ][ 'password' ]; // <--- password crypted in the DB.
                            $password_decrypted = self::get_password($password_crypted, self::PASSWORD_DECRYPT);

                            //d( "original",	  $_data[ 'password' ]	);
                            //d( "crypted",	    $password_crypted		    );
                            //d( "decrypted",	  $password_decrypted	    );
                            //d( "endecrypted",	self::get_password( $_data[ 'password' ], self::PASSWORD_ENCRYPT ) );

                            if ($_data[ 'password' ] !== $password_decrypted) {
                                $_a = null;
                                \Stripe\Controller::error(__CLASS__, __METHOD__, __LINE__, "Password doesn't match", [ 'sql' => self::$sql, 'data' => $_data ], true, null);
                            }
                        }
                    } else {
                        \Stripe\Controller::error(__CLASS__, __METHOD__, __LINE__, "SQL Error", [ "sql" => self::$sql, "error" => (is_callable([ $DB, 'errorInfo' ]) === true) ? $DB->errorInfo() : '' ], true, null);
                    }
                } catch (\PDOException $err) {
                    \Stripe\Controller::error(__CLASS__, __METHOD__, __LINE__, $err->getMessage(), self::$sql, true, $err);
                }
            } else {
                \Stripe\Controller::error(__CLASS__, __METHOD__, __LINE__, "Database method 'prepare' is not accessible", self::$sql, true);
            }
        } else {
            \Stripe\Controller::error(__CLASS__, __METHOD__, __LINE__, "No valid accountID", $_data, true, null);
        }

        return $_a;
    }

    public static function create($_data)
    {
        if (
      isset($_data[ 'email' ]) &&
      !empty($_data[ 'email' ]) &&
      is_string($_data[ 'email' ]) &&
      strlen($_data[ 'email' ]) > 0
    ) {
            if (
       isset($_data[ 'password' ]) &&
       !empty($_data[ 'password' ]) &&
       is_string($_data[ 'password' ]) &&
       strlen($_data[ 'password' ]) > 0
      ) {
                $password_crypted = self::get_password($_data[ 'password' ], self::PASSWORD_ENCRYPT);
            }

            self::$sql =
      " INSERT IGNORE INTO ". DB_BASE . "." . self::TABLE .
      " ( ".
           ((!isset($_data[ 'id'         ])) ? '' : "id,") .
        ((!isset($_data[ 'first_name' ])) ? '' : "first_name,") .
        ((!isset($_data[ 'last_name'  ])) ? '' : "last_name,") .
        ((!isset($password_crypted)) ? '' : "password,") .
                                                                                 "email," 		   .
                                                                                 "date_created"  .
      " ) ".
      " VALUES ".
      " ( ".
          ((!isset($_data[ 'id'         ])) ? '' : 		 	intval($_data[ 'id'         ]) . ",") .
        ((!isset($_data[ 'first_name' ])) ? '' : "'" . \Strings::DBTextClean($_data[ 'first_name' ]) . "',") .
        ((!isset($_data[ 'last_name'  ])) ? '' : "'" . \Strings::DBTextClean($_data[ 'last_name'  ]) . "',") .
        ((!isset($password_crypted)) ? '' : "'" . \Strings::DBTextClean($password_crypted) . "',") .
                                                                                "'" . \Strings::DBTextClean($_data[ 'email'      ]) . "',"   .
                                                                                      "'" . \Strings::DBCurrentDate()						  			    . "' "   .
      " ) " .
      (
          (isset($_data[ 'id' ]) && intval($_data[ 'id' ]) > 0) ?
      " ON DUPLICATE KEY UPDATE ".
          ((!isset($_data[ 'first_name' ])) ? '' : "first_name = '" . \Strings::DBTextClean($_data[ 'first_name'       ]) . "',") .
          ((!isset($_data[ 'last_name'  ])) ? '' : "last_name = '" .  \Strings::DBTextClean($_data[ 'last_name'        ]) . "',") .
          ((!isset($password_crypted)) ? '' : "password = '" . 	 \Strings::DBTextClean($password_crypted) . "',") .
          ((!isset($_data[ 'email'      ])) ? '' : "email = '" . 	 	 \Strings::DBTextClean($_data[ 'email'            ]) . "',") .
                                                      "id = " .          ((isset($_data[ 'id' ]) && intval($_data[ 'id' ]) > 0) ? intval($_data[ 'id' ]) : "LAST_INSERT_ID( id )") . ";"
        : ';'
      );

            $DB = \Stripe\Model::$DB;
            $DB = ((!$DB) ? \Stripe\Model::get_db() : $DB);

            if (is_callable([ $DB, 'prepare' ])) {
                try {
                    $query = $DB->prepare(self::$sql);

                    if (!$query) {
                        self::error(__CLASS__, __METHOD__, self::$sql, (is_callable([ $DB, 'errorInfo' ]) === true) ? $DB->errorInfo() : '');
                    }
                    if ($query->execute()) {
                        $last_id = intval(
                (isset($_data[ 'id' ]) && intval($_data[ 'id' ]) > 0) ?
              $_data[ 'id' ]
              :
              $DB->lastInsertId()
            );

                        if (
              $last_id > 0 &&
                        isset($_data[ 'password' ]) &&
                        isset($_data[ 'email'    ])
                    ) {
                            $account_row_ = self::read([
                            'id' 		   => $last_id,
                            'email'		 => $_data[ 'email'    ],
                            'password' => $_data[ 'password' ]
                        ]) ;

                            $account_row_ = ((isset($account_row_)) ? $account_row_[ 0 ] : []);

                            if (
                isset($account_row_[ 'id' ]) &&
                intval($account_row_[ 'id' ]) > 0
              ) {
                                // -- initialize here: $_SESSION[ 'ACCOUNT' ][ 'id' ]
                                self::set_session($account_row_);
                            }
                        }
                        return $last_id;
                    } else {
                        \Stripe\Controller::error(__CLASS__, __METHOD__, __LINE__, "SQL Error", [ "sql" => self::$sql, "error" => (is_callable([ $DB, 'errorInfo' ]) === true) ? $DB->errorInfo() : '' ], true, null);
                    }
                } catch (\PDOException $err) {
                    \Stripe\Controller::error(__CLASS__, __METHOD__, __LINE__, $err->getMessage(), self::$sql, true, $err);
                }
            }
        }
        return -1;
    }

    /**
     * Encrypt/Decrypt a password.
       * @param {string} $passwor to encrypt or decrypt
       * @param {string} $action type of action to proceed: 'ENCRYPT' or 'DECRYPT'
       *
       * @return {string} the value of the password.
     */
    public static function get_password($password, $action = 'encrypt')
    {
        switch ($action) {
            default:
            case self::PASSWORD_ENCRYPT: return \Crypter::STRING_encrypt($password, LOCAL_ENCRIPTION_KEY); break;
            case self::PASSWORD_DECRYPT: return \Crypter::STRING_decrypt($password, LOCAL_ENCRIPTION_KEY); break;
        }
    }

    public static function set_session($account_row = [])
    {
        try {
            if (
        isset($account_row) &&
        !empty($account_row)
      ) {
                $_SESSION[ 'ACCOUNT' ] = $account_row;
                $_SESSION[ 'TIME'    ] = date(TIMESTAMP_STRUCTURE);
            } else {
                $_SESSION[ 'ACCOUNT' ] = null;
                unset($_SESSION[ 'ACCOUNT' ]);
                \Stripe\Controller::error(__CLASS__, __METHOD__, __LINE__, "Invalid account info, unset session", [ 'account_row' => $account_row ], true, null);
            }
        } catch (\Exception $err) {
            \Stripe\Controller::error(__CLASS__, __METHOD__, __LINE__, $err->getMessage(), null, true, $err);
        }
    }

    public static function login($accountID = null)
    {
        if (
      isset($accountID) &&
      intval($accountID) > 0
    ) {
            $row_ = self::read([ 'id' => $accountID ]);
            $row_ = ((isset($row_) && !empty($row_)) ? $row_[ 0 ] : []);

            if (isset($row_[ 'id' ]) && intval($row_[ 'id' ]) > 0) {
                self::set_session($row_);
            }
        }
    }

    public static function logout()
    {
        self::set_session(null);
    }

    public static function update_session()
    {
        if (
      isset($_SESSION[ 'ACCOUNT' ][ 'id' ]) &&
      intval($_SESSION[ 'ACCOUNT' ][ 'id' ]) > 0
    ) {
            $r_ = self::read([ "id" => $_SESSION[ 'ACCOUNT' ][ 'id' ] ]);

            if (
        isset($r_) &&
        !empty($r_) &&
        count($r_) === 1
      ) {
                self::set_session($r_[ 0 ]);
                return true;
            }
        }
        return false;
    }

    public static function search($_data = [])
    {
        if (
      isset($_data[ 'password' ]) &&
      strlen($_data[ 'password' ]) > 0
    ) {
            $password_crypted = self::get_password($_data[ 'password' ], self::PASSWORD_ENCRYPT);
        }

        $_a = [];

        self::$sql =
    " SELECT * ".
    " FROM ". DB_BASE . "." . self::TABLE .
    " WHERE id > 0 ".
        ((isset($_data[ 'id'         ]) && intval($_data[ 'id'         ]) > 0) ? " AND id = ".  			      intval($_data[ 'id'         ]) . "   " : '') .
         ((isset($_data[ 'first_name' ]) && strlen($_data[ 'first_name' ]) > 0) ? " AND first_name LIKE '%". \Strings::DBTextClean($_data[ 'first_name' ]) . "%' " : '') .
      ((isset($_data[ 'last_name'  ]) && strlen($_data[ 'last_name'  ]) > 0) ? " AND last_name LIKE '%".  \Strings::DBTextClean($_data[ 'last_name'  ]) . "%' " : '') .
      ((isset($_data[ 'email'      ]) && strlen($_data[ 'email'      ]) > 0) ? " AND email = '".  		    \Strings::DBTextClean($_data[ 'email'      ]) . "'  " : '') .
      ((isset($_data[ 'password'   ]) && strlen($_data[ 'password'   ]) > 0) ? " AND password = '".  	    \Strings::DBTextClean($password_crypted) . "'  " : '') .
    " LIMIT 100;";

        if (is_callable([ self::$DB, 'prepare' ])) {
            try {
                $query = self::$DB->prepare(self::$sql);

                if (!$query) {
                    self::error(__CLASS__, __METHOD__, self::$sql, (is_callable([ $DB, 'errorInfo' ]) === true)? $DB->errorInfo() : '');
                }
                if ($query->execute()) {
                    for ($i = 0 ; $row = $query->fetch() ; $i++) {
                        $_a = self::_list($_a, $row, $i);
                    }
                } else {
                    \Stripe\Controller::error(__CLASS__, __METHOD__, __LINE__, "SQL Error", [ "sql" => self::$sql, "error" => (is_callable([ self::$DB, 'errorInfo' ]) === true) ? self::$DB->errorInfo() : '' ], true, null);
                }
            } catch (\PDOException $err) {
                \Stripe\Controller::error(__CLASS__, __METHOD__, __LINE__, $err->getMessage(), self::$sql, true, $err);
            }
        }
        return $_a;
    }

    public static function delete($_data = [])
    {
        if (
      isset($_data[ 'id' ]) &&
      self::_control($_data[ 'id' ]) === true
    ) {
            self::$sql =
      " DELETE FROM ". DB_BASE . "." . self::TABLE .
      " WHERE id = " . intval($_data[ 'id' ]) .
      ";";

            if (is_callable([ self::$DB, 'prepare' ]) === true) {
                try {
                    $query = self::$DB->prepare(self::$sql);

                    if (!$query) {
                        self::error(__CLASS__, __METHOD__, self::$sql, (is_callable([ $DB, 'errorInfo' ]) === true) ? $DB->errorInfo() : '');
                    }
                    if ($query->execute() === true) {
                        return true;
                    } else {
                        self::error(__CLASS__, __METHOD__, self::$sql, (is_callable([ $DB, 'errorInfo' ]) === true) ? $DB->errorInfo() : '');
                    }
                } catch (\PDOException $err) {
                    \Stripe\Controller::error(__CLASS__, __METHOD__, __LINE__, $err->getMessage(), self::$sql, true, $err);
                }
            }
        }
        return false;
    }

    /**
       * Get the Gravatar image URL from a specified email address.
       *
       * @param {string} $email The email address
       * @param {string} $s 	Size in pixels, defaults to 80px [ 1 - 2048 ]
       * @param {string} $d 	Default imageset to use [ 404 | mm | identicon | monsterid | wavatar ]
       * @param {string} $r 	Maximum rating (inclusive) [ g | pg | r | x ]
       * @return {String} containing the image URL
       *
       * @source http://gravatar.com/site/implement/images/php/
       */
    public static function get_image_url($email, $s = 35, $d = 'mm', $r = 'g')
    {
        return 'https://www.gravatar.com/avatar/' .
        md5(strtolower(trim($email))) .
        "?s=" . $s .
        "&d=" . $d .
        "&r=" . $r;
    }



    // -- Private Methods

    private static function _control($id = null)
    {
        return ((isset($id) && !empty($id) && intval($id) > 0) ? true : false);
    }

    private static function _list($_a, $row, $i = 0)
    {
        if (
      isset($row) &&
      !empty($row)
    ) {
            array_push($_a, [
        'id' 				   => ((isset($row[ "id"           ])) ? intval($row[ "id"           ]) : null),
        'first_name' 	 => ((isset($row[ "first_name"   ])) ? \Strings::DBTextToWeb($row[ "first_name"   ]) : null),
        'last_name' 	 => ((isset($row[ "last_name"    ])) ? \Strings::DBTextToWeb($row[ "last_name"    ]) : null),
        'password' 		 => ((isset($row[ "password"     ])) ? \Strings::DBTextToWeb($row[ "password"     ]) : null), // self::get_password( , self::PASSWORD_DECRYPT )
        'date_created' => ((isset($row[ "date_created" ])) ? \Strings::DBTextClean($row[ "date_created" ]) : null),
        'email' 			 => ((isset($row[ "email"        ])) ? \Strings::DBTextToWeb($row[ "email"        ]) : null)
      ]);
        }
        return $_a;
    }
}
