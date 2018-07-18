<?php
namespace Stripe;

if ( !AUTHORIZED ) {
  die("Hacking Attempt [Accounts_model] : ". $_SERVER['REMOTE_ADDR']);
}

/**
 * Database structure:
 *
 * CREATE TABLE `account` (
 *  `id`            bigint(15) UNSIGNED NOT NULL COMMENT 'Unique account identifier',
 *  `email`         varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
 *  `password`      varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
 *  `first_name`    varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
 *  `last_name`     varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
 *  `date_created`  timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
 * ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
 *
 * ALTER TABLE `account`
 *   ADD PRIMARY KEY (`id`),
 *   ADD UNIQUE KEY `email` (`email`),
 *   ADD KEY `first_name` (`first_name`,`last_name`);
 *
 * ALTER TABLE `account`
 *   MODIFY `id` bigint(15) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Unique account identifier', AUTO_INCREMENT=1;
 *   COMMIT;
 */
final class Account_model extends \Stripe\Model implements iCRUDS
{
  const TABLE = 'account';

  const PASSWORD_ENCRYPT = 'encrypt';
  const PASSWORD_DECRYPT = 'decrypt';

  public static function read( $_data )
  {
    $_a = [];

    if ( isset( $_data[ 'id' ] ) && self::_control( $_data[ 'id' ] ) === TRUE ) {
      self::$sql =
      " SELECT * ".
      " FROM ". DB_BASE . "." . self::TABLE .
      " WHERE id = '". intval( $_data[ 'id' ] ) . "' " .
     		( ( isset( $_data[ 'email' ] ) && !empty( $_data[ 'email' ] ) && is_string( $_data[ 'email' ] ) && strlen( $_data[ 'email' ] ) > 0 ) ? " AND email='" . \Strings::DBTextClean( $_data[ 'email' ] ) . "' " : '' ) .
     	" LIMIT 100;";

      if ( is_callable([ self::$DB, 'prepare' ]) === TRUE ) {
        try {
          $query = self::$DB->prepare( self::$sql );

          if ( !$query ) {
            self::error( __CLASS__, __METHOD__, self::$sql, ( is_callable([ $DB, 'errorInfo' ]) === TRUE ) ? $DB->errorInfo() : '' );
          }
    			if ( $query->execute() ) {
    	      for ( $i = 0 ; $row = $query->fetch() ; $i++ ) {
    	        $_a = self::_list( $_a, $row, $i );
            }

            // -- Control the account's password only after having checked that the email was in the DB.
            // -- The password is only encrypted in the DB, the data sent through the local API is always decrypted.
            if (
              isset( $_data[ 'email'    ] ) && !empty( $_data[ 'email' 		] ) && is_string( $_data[ 'email' 	 ] ) && strlen( $_data[ 'email'  	 ] ) > 0 &&
              isset( $_data[ 'password' ] ) && !empty( $_data[ 'password' ] ) && is_string( $_data[ 'password' ] ) && strlen( $_data[ 'password' ] ) > 0 &&
              isset( $_a ) && !empty( $_a ) && count( $_a ) === 1
            ) {
              $password_crypted = $_a[ 0 ][ 'password' ]; // <--- password crypted in the DB.
            	$password_decrypted = self::get_password( $password_crypted, self::PASSWORD_DECRYPT );

              if ( $_data[ 'password' ] !== $password_decrypted ) {
                $_a = NULL;
                \Stripe\Controller::error( __CLASS__, __METHOD__, __LINE__, "Password doesn't match", [ 'sql' => self::$sql, 'data' => $_data ], TRUE, NULL );
              }
      		  }
          } else {
            \Stripe\Controller::error( __CLASS__, __METHOD__, __LINE__, "SQL Error", [ "sql" => self::$sql, "error" => ( is_callable([ $DB, 'errorInfo' ]) === TRUE ) ? $DB->errorInfo() : '' ], TRUE, NULL );
          }
        }
        catch ( \PDOException $err ) {
          \Stripe\Controller::error( __CLASS__, __METHOD__, __LINE__, $err->getMessage(), self::$sql, TRUE, $err );
        }
      } else {
        \Stripe\Controller::error( __CLASS__, __METHOD__, __LINE__, "Database method 'prepare' is not accessible", self::$sql, TRUE );
      }
    } else {
      \Stripe\Controller::error( __CLASS__, __METHOD__, __LINE__, "No valid accountID", $_data, TRUE, NULL );
    }
    return $_a;
  }

  public static function create( $_data )
  {
    if (
      isset(     $_data[ 'email' ] ) &&
      !empty(    $_data[ 'email' ] ) &&
      is_string( $_data[ 'email' ] ) &&
      strlen(    $_data[ 'email' ] ) > 0
    ) {
    	if (
  	   isset(     $_data[ 'password' ] ) &&
       !empty(    $_data[ 'password' ] ) &&
       is_string( $_data[ 'password' ] ) &&
  	   strlen(    $_data[ 'password' ] ) > 0
      ) {
    		$password_crypted = self::get_password( $_data[ 'password' ], self::PASSWORD_ENCRYPT );
			}

      self::$sql =
      " INSERT IGNORE INTO ". DB_BASE . "." . self::TABLE .
      " ( ".
       	( ( !isset(  $_data[ 'id'         ] ) ) ? '' : "id," 			   ) .
        ( ( !isset(  $_data[ 'first_name' ] ) ) ? '' : "first_name," ) .
        ( ( !isset(  $_data[ 'last_name'  ] ) ) ? '' : "last_name,"  ) .
        ( ( !isset(  $password_crypted      ) ) ? '' : "password," 	 ) .
          												 	                   "email," 		   .
        												 	                     "date_created"  .
      " ) ".
      " VALUES ".
      " ( ".
      	( ( !isset( $_data[ 'id'         ] ) ) ? '' : 		 	intval(      					 $_data[ 'id'         ] ) . ","  ) .
        ( ( !isset( $_data[ 'first_name' ] ) ) ? '' : "'" . \Strings::DBTextClean( $_data[ 'first_name' ] ) . "'," ) .
        ( ( !isset( $_data[ 'last_name'  ] ) ) ? '' : "'" . \Strings::DBTextClean( $_data[ 'last_name'  ] ) . "'," ) .
        ( ( !isset( $password_crypted      ) ) ? '' : "'" . \Strings::DBTextClean( $password_crypted		  ) . "'," ) .
              									 		 		              "'" . \Strings::DBTextClean( $_data[ 'email'      ] ) . "',"   .
																                      "'" . \Strings::DBCurrentDate()						  			    . "' "   .
      " ) " .
      ( ( isset( $_data[ 'id' ] ) && intval( $_data[ 'id' ] ) > 0 ) ?
      " ON DUPLICATE KEY UPDATE ".
      	( ( !isset( $_data[ 'first_name' ] ) ) ? '' : "first_name = '" . \Strings::DBTextClean( $_data[ 'first_name'       ] ) . "'," ) .
      	( ( !isset( $_data[ 'last_name'  ] ) ) ? '' : "last_name = '" .  \Strings::DBTextClean( $_data[ 'last_name'        ] ) . "'," ) .
      	( ( !isset( $password_crypted   	 ) ) ? '' : "password = '" . 	 \Strings::DBTextClean( $password_crypted			       ) . "'," ) .
      	( ( !isset( $_data[ 'email'      ] ) ) ? '' : "email = '" . 	 	 \Strings::DBTextClean( $_data[ 'email'            ] ) . "'," ) .
                                                      "id = " .          ( ( isset( $_data[ 'id' ] ) && intval( $_data[ 'id' ] ) > 0 ) ? intval( $_data[ 'id' ] ) : "LAST_INSERT_ID( id )" ) . ";"
        : ';'
      );

		  $DB = \Stripe\Model::$DB;
      $DB = ( ( !$DB ) ? \Stripe\Model::get_db() : $DB );

      if ( is_callable([ $DB, 'prepare' ]) ) {
        try {
          $query = $DB->prepare( self::$sql );

          if ( !$query ) { self::error( __CLASS__, __METHOD__, self::$sql, ( is_callable([ $DB, 'errorInfo' ]) === TRUE ) ? $DB->errorInfo() : '' ); }
    			if ( $query->execute() ) {
            $last_id = intval( ( isset( $_data[ 'id' ] ) && intval( $_data[ 'id' ] ) > 0 ) ?
              $_data[ 'id' ]
              :
              $DB->lastInsertId()
            );

    				if (
              $last_id > 0 &&
    					isset( $_data[ 'password' ] ) &&
    					isset( $_data[ 'email'    ] )
    				) {
    					$account_row_ = self::read([
    						'id' 		   => $last_id,
    						'email'		 => $_data[ 'email'    ],
    						'password' => $_data[ 'password' ]
    					]) ;

    					$account_row_ = ( ( isset( $account_row_ ) ) ? $account_row_[ 0 ] : [] );

    					if (
                isset(  $account_row_[ 'id' ] ) &&
                intval( $account_row_[ 'id' ] ) > 0
              ) {
                self::set_session( $account_row_ );
                self::send_email( $account_row_ );
    					}
    				}
         		return $last_id;
          } else {
            \Stripe\Controller::error( __CLASS__, __METHOD__, __LINE__, "SQL Error", [ "sql" => self::$sql, "error" => ( is_callable([ $DB, 'errorInfo' ]) === TRUE ) ? $DB->errorInfo() : '' ], TRUE, NULL );
          }
        }
        catch ( \PDOException $err ) {
          \Stripe\Controller::error( __CLASS__, __METHOD__, __LINE__, $err->getMessage(), self::$sql, TRUE, $err );
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
	public static function get_password( $password, $action = 'encrypt' )
	{
		switch ( $action ) {
			default:
			case self::PASSWORD_ENCRYPT :
        return \Crypter::STRING_encrypt( $password, LOCAL_ENCRIPTION_KEY );
			case self::PASSWORD_DECRYPT :
        return \Crypter::STRING_decrypt( $password, LOCAL_ENCRIPTION_KEY );
		}
	}

  public static function set_session( $account_row = [] )
	{
    try {
  		if (
        isset(  $account_row ) &&
        !empty( $account_row )
      ) {
        $_SESSION[ 'ACCOUNT' ] = $account_row;
  			$_SESSION[ 'TIME'    ] = date( TIMESTAMP_STRUCTURE );
  		} else {
        $_SESSION[ 'ACCOUNT' ] = NULL;
  			unset( $_SESSION[ 'ACCOUNT' ] );
        \Stripe\Controller::error( __CLASS__, __METHOD__, __LINE__, "Invalid account info, unset session", [ 'account_row' => $account_row ], TRUE, NULL );
  		}
    } catch ( \Exception $err ) {
      \Stripe\Controller::error( __CLASS__, __METHOD__, __LINE__, $err->getMessage(), NULL, TRUE, $err );
    }
	}

	public static function login( $accountID = NULL )
	{
		if (
      isset(  $accountID ) &&
      intval( $accountID ) > 0
    ) {
			$row_ = self::read([ 'id' => $accountID ]);
			$row_ = ( ( isset( $row_ ) && !empty( $row_ ) ) ? $row_[ 0 ] : [] );

			if ( isset( $row_[ 'id' ] ) && intval( $row_[ 'id' ] ) > 0 ) {
				self::set_session( $row_ );
      }
		}
	}

	public static function logout()
	{
		self::set_session( NULL );
	}

	public static function update_session()
	{
		if (
      isset(  $_SESSION[ 'ACCOUNT' ][ 'id' ] ) &&
      intval( $_SESSION[ 'ACCOUNT' ][ 'id' ] ) > 0
    ) {
			$r_ = self::read([ "id" => $_SESSION[ 'ACCOUNT' ][ 'id' ] ]);

			if (
        isset(  $r_ ) &&
        !empty( $r_ ) &&
        count(  $r_ ) === 1
      ) {
				self::set_session( $r_[ 0 ] );
        return TRUE;
      }
		}
    return FALSE;
	}

	public static function search( $_data = [] )
  {
  	if (
      isset(  $_data[ 'password' ] ) &&
      strlen( $_data[ 'password' ] ) > 0
    ) {
  		$password_crypted = self::get_password( $_data[ 'password' ], self::PASSWORD_ENCRYPT );
		}

    $_a = [];

    self::$sql =
    " SELECT * ".
   	" FROM ". DB_BASE . "." . self::TABLE .
   	" WHERE id > 0 ".
   		( ( isset( $_data[ 'id'         ] ) && intval( $_data[ 'id'         ] ) > 0  ) ? " AND id = ".  			      intval( 	             $_data[ 'id'         ] ) . "   " : '' ) .
     	( ( isset( $_data[ 'first_name' ] ) && strlen( $_data[ 'first_name' ] ) > 0  ) ? " AND first_name LIKE '%". \Strings::DBTextClean( $_data[ 'first_name' ] ) . "%' " : '' ) .
      ( ( isset( $_data[ 'last_name'  ] ) && strlen( $_data[ 'last_name'  ] ) > 0  ) ? " AND last_name LIKE '%".  \Strings::DBTextClean( $_data[ 'last_name'  ] ) . "%' " : '' ) .
      ( ( isset( $_data[ 'email'      ] ) && strlen( $_data[ 'email'      ] ) > 0  ) ? " AND email = '".  		    \Strings::DBTextClean( $_data[ 'email'      ] ) . "'  " : '' ) .
      ( ( isset( $_data[ 'password'   ] ) && strlen( $_data[ 'password'   ] ) > 0  ) ? " AND password = '".  	    \Strings::DBTextClean( $password_crypted		  ) . "'  " : '' ) .
    " LIMIT 100;";

    if ( is_callable([ self::$DB, 'prepare' ]) ) {
      try {
        $query = self::$DB->prepare( self::$sql );

        if ( !$query ) { self::error( __CLASS__, __METHOD__, self::$sql, ( is_callable([ $DB, 'errorInfo' ]) === TRUE )? $DB->errorInfo() : '' ); }
    		if ( $query->execute() ) {
          for ( $i = 0 ; $row = $query->fetch() ; $i++ ) {
  	        $_a = self::_list( $_a, $row, $i );
          }
        } else {
          \Stripe\Controller::error( __CLASS__, __METHOD__, __LINE__, "SQL Error", [ "sql" => self::$sql, "error" => ( is_callable([ self::$DB, 'errorInfo' ]) === TRUE ) ? self::$DB->errorInfo() : '' ], TRUE, NULL );
        }
      }
      catch ( \PDOException $err ) {
        \Stripe\Controller::error( __CLASS__, __METHOD__, __LINE__, $err->getMessage(), self::$sql, TRUE, $err );
      }
    }
    return $_a;
  }

  public static function delete( $_data = [] )
  {
    if (
      isset( $_data[ 'id' ] ) &&
      self::_control( $_data[ 'id' ] ) === TRUE
    ) {
      self::$sql =
      " DELETE FROM ". DB_BASE . "." . self::TABLE .
      " WHERE id = " . intval( $_data[ 'id' ] ) .
      ";";

      if ( is_callable([ self::$DB, 'prepare' ]) === TRUE ) {
        try {
          $query = self::$DB->prepare( self::$sql );

          if ( !$query ) { self::error( __CLASS__, __METHOD__, self::$sql, ( is_callable([ $DB, 'errorInfo' ]) === TRUE ) ? $DB->errorInfo() : '' ); }
    			if ( $query->execute() === TRUE ) {
            return TRUE;
          } else {
            self::error( __CLASS__, __METHOD__, self::$sql, ( is_callable([ $DB, 'errorInfo' ]) === TRUE ) ? $DB->errorInfo() : '' );
          }
        }
        catch ( \PDOException $err ) {
          \Stripe\Controller::error( __CLASS__, __METHOD__, __LINE__, $err->getMessage(), self::$sql, TRUE, $err );
        }
      }
    }
    return FALSE;
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
	public static function get_image_url( $email, $s = 35, $d = 'mm', $r = 'g' )
	{
    return 'https://www.gravatar.com/avatar/' .
		md5( strtolower( trim( $email ) ) ) .
		"?s=" . $s .
		"&d=" . $d .
		"&r=" . $r;
	}



  // -- Private Methods

  /**
   * Send an emailto the user once his account have been created.
   *
   * @param {array} Array of data of DB fields of the current user.
   * @retun {boolean} TRUE in case of sucess, FALSE otherwise.
   */
  private static function send_email( Array $account_row_ = [] )
  {
    \Stripe\Email_validate_view::$email = $account_row_[ 'email' ];

    return \Stripe\Emails_model::send(
      $account_row_[ 'email' ],
      "Welcome to the Todo App!",
      \Stripe\Email_validate_view::output()
    );
  }

  /**
   * Control if tbe current ID is valid.
   *
   * @param {int} account identifier to valid.
   * @return {boolean} TRUE in case of sucess, FALSE otherwise.
   */
  private static function _control( $id = NULL )
  {
    return ( ( isset( $id ) && !empty( $id ) && intval( $id ) > 0 ) ? TRUE : FALSE );
  }

  private static function _list( $_a, $row, $i = 0 )
  {
    if (
      isset(  $row ) &&
      !empty( $row )
    ) {
   		array_push( $_a, [
        'id' 				   => ( ( isset( $row[ "id"           ] ) ) ? intval(	               $row[ "id"           ] ) : NULL ),
        'first_name' 	 => ( ( isset( $row[ "first_name"   ] ) ) ? \Strings::DBTextToWeb( $row[ "first_name"   ] ) : NULL ),
        'last_name' 	 => ( ( isset( $row[ "last_name"    ] ) ) ? \Strings::DBTextToWeb( $row[ "last_name"    ] ) : NULL ),
        'password' 		 => ( ( isset( $row[ "password"     ] ) ) ? \Strings::DBTextToWeb( $row[ "password"     ] ) : NULL ),
        'date_created' => ( ( isset( $row[ "date_created" ] ) ) ? \Strings::DBTextClean( $row[ "date_created" ] ) : NULL ),
        'email' 			 => ( ( isset( $row[ "email"        ] ) ) ? \Strings::DBTextToWeb( $row[ "email"        ] ) : NULL )
      ]);
    }
    return $_a;
  }
}
