<?php
namespace Stripe;

if(!AUTHORIZED) {
  die("Hacking Attempt: ". $_SERVER['REMOTE_ADDR']);
}

final class Account_controller extends \Stripe\Controller
{
  /**
   * Singn in (or login if account already exists).
   * @param {string} URL where to redirect after signin/login.
   * @param {string} account email
   * @param {string} account password
   * @param {Array<any>} [OPTIONAL] array of data that can be assigned to the account while signing-in.
   *                     [
   *                       'first_name' {string}
   *                       'last_name'  {string}
   *                     ]
   * @return {array}
   */
  public static function get_signin( $redirect = NULL, $email = NULL, $password = NULL, Array $OPTION_ = [] )
  {
    $RESULT_ = [];

    self::error( __CLASS__, __METHOD__, __LINE__, 'Sign-in', [ 'password' => $password, 'email' => $email ], FALSE );

    if ( isset( $email ) && \Strings::isValidEmail( $email ) ) {
      if ( isset( $password ) && strlen( $password ) > 5 ) {
	      $account_row = \Stripe\Account_model::search([ "email" => $email ]);

	      $pswd = $password;

        $first_name = ( ( isset( $OPTION_[ 'first_name' ] ) ) ? $OPTION_[ 'first_name' ] : NULL );
        $last_name  = ( ( isset( $OPTION_[ 'last_name'  ] ) ) ? $OPTION_[ 'last_name'  ] : NULL );

        if ( isset( $password ) ) {
			    $password_crypted = \Stripe\Account_model::get_password( $password, \Stripe\Account_model::PASSWORD_ENCRYPT );

			    if ( ( !isset( \Crypter::$errors ) || ( isset( \Crypter::$errors ) && empty( \Crypter::$errors ) ) ) && strlen( $password_crypted ) > 5 ) {
				    // -- if the account doesn't exists
  					if (
							!isset( $account_row )
							||
							(
								isset( $account_row ) &&
								empty( $account_row )
							)
						) {
  						$ac_id = \Stripe\Account_model::create([
  							"email" 		 => ( ( isset( $email      ) ) ? $email      : NULL ),
  							"password" 	 => ( ( isset( $password   ) ) ? $password   : NULL ),
  							"first_name" => ( ( isset( $first_name ) ) ? $first_name : NULL ),
  							"last_name"	 => ( ( isset( $last_name  ) ) ? $last_name  : NULL )
  						]);

  						if (
						   isset(  $ac_id ) &&
						   intval( $ac_id ) > 0
              ) {
  							$RESULT_[ 'result' ] = "Your account have been created!";
  							$RESULT_[ 'error'  ] = NULL;

  							$RESULT_ = self::get_login( $redirect, $email, $password );
  						} else {
						    $RESULT_[ 'error' ] = self::error( __CLASS__, __METHOD__, __LINE__, "An error occured while creating your account please try later." . $ac_id, \Stripe\Account_model::$sql );
  						}
  					}

            // -- otherwise if the account already exists
  					else if (
					   isset(  $account_row ) &&
					   !empty( $account_row ) &&
					   count(  $account_row ) == 1
            ) {
  						$RESULT_ = self::get_login(
						    $redirect,
						    $email,
						    $password
              );

  						if ( !isset( $RESULT_[ 'error' ] ) || ( isset( $RESULT_[ 'error' ] ) && empty( $RESULT_[ 'error' ] ) ) ) {
  							// OK here, nothing to do..
  						} else { self::error( __CLASS__, __METHOD__, __LINE__, "Impossible to Login", $RESULT_ ); }
  					} else { $RESULT_[ 'error' ] = self::error( __CLASS__, __METHOD__, __LINE__, "An account already exists with this email." );}
  				} else { $RESULT_[ 'error' ] = self::error( __CLASS__, __METHOD__, __LINE__, "An error occured while encoding your password " . @implode(',', \Crypter::$errors ), \Crypter::$errors );}
       } else { $RESULT_[ 'error' ] = self::error( __CLASS__, __METHOD__, __LINE__, "An error occured while encoding your default password " . $password );}
			} else { $RESULT_[ 'error' ] = self::error( __CLASS__, __METHOD__, __LINE__, "Your password must have more then five characters.", $password, FALSE );}
		} else { $RESULT_[ 'error' ] = self::error( __CLASS__, __METHOD__, __LINE__, "Your email doesn't seem to be valid. " . $email, $email, FALSE );}

		return $RESULT_;
	}

  /**
   * Do an account login
   * @param {string} URL where to redirect after login
   * @param {string} account email
   * @param {string} account password
   *
   * @return {array}
   */
	public static function get_login( $redirect = NULL, $email = NULL, $password = NULL )
	{
		$RESULT_ = [];
    $RESULT_['data'] = [
      "email"=>$email,
      "password"=>$password
    ];

		if ( isset( $email ) && !empty( $email ) && is_string( $email ) && \Strings::isValidEmail( $email ) === TRUE ) {
			if ( isset( $password ) && !empty( $password ) && is_string( $password ) && strlen( $password ) > 5 ) {
				$account_row = \Stripe\Account_model::search([ "email" => $email ]);

				if (
			    isset(  $account_row ) &&
			    !empty( $account_row ) &&
			    count(  $account_row ) === 1
        ) {
					$acc_row = \Stripe\Account_model::read([
						"id"			 => $account_row[ 0 ][ 'id' ],
						"email" 	 => $email,
						"password" => $password,
					], FALSE );

					if (
				   isset(  $acc_row ) &&
				   !empty( $acc_row ) &&
				   count(  $acc_row ) === 1
          ) {
						\Stripe\Account_model::set_session( $acc_row[ 0 ] );

						if ( \Strings::isValidURL( $redirect ) === TRUE ) {
							\Stripe\Controller::redirect( $redirect );
            }

            $account_ = $_SESSION[ 'ACCOUNT' ];

						$RESULT_[ 'result' ] = $account_;
					} else {$RESULT_[ 'error' ] = self::error( __CLASS__, __METHOD__, __LINE__, "Your login / password doesn't match", FALSE );}
				} else { $RESULT_[ 'error' ] = self::error( __CLASS__, __METHOD__, __LINE__, "Your email doesn't match with any account", $email, FALSE );}
			} else { $RESULT_[ 'error' ] = self::error( __CLASS__, __METHOD__, __LINE__, "Your password must have more then five characters.", $password, FALSE );}
		} else { $RESULT_[ 'error' ] = self::error( __CLASS__, __METHOD__, __LINE__, "Your email is not valid.", $email, FALSE );}

		return $RESULT_;
	}

  /**
   * Do an account logout
   * @param {string} URL where to redirect after logout
   *
   * @return {void}
   */
	public static function get_logout( $redirect = TRUE )
	{
		\Stripe\Account_model::logout();

		if ( $redirect == TRUE ) {
			self::redirect( '/' );
    }
		return NULL;
	}

  /**
   * Send an email to the user to resquieu its password.
   *
   * @param {string} Account email where to send the forgotten password.
   */
	public static function get_forgot( $email = NULL )
	{
		if ( !isset( $email ) ) { return NULL; }

		$RESULT_ = [];

		if ( \Strings::isValidEmail( $email ) === TRUE ) {
			$account_row = \Stripe\Account_model::search([ 'email' => $email ]);

			if (
				isset(  $account_row ) &&
				!empty( $account_row ) &&
				count(  $account_row ) === 1
			) {
				$account_id = ( ( isset( $account_row[0][ 'id' ] ) ) ? $account_row[0][ 'id' ] : 0 );

				if ( $account_id > 0 )
				{
					$subject = "TodoList App password.";

					$email_view 				   = new \Stripe\Email_forgotten_view();
					$email_view::$email    = $email;
          $email_view::$password = $account_row[0][ 'password' ];

					if ( \Stripe\Emails_model::send( $email, $subject, $email_view::output() ) === TRUE ) {
						$RESULT_[ 'result' ] = sprintf( "Your password have been sent to %s.", $email );
					} else { $RESULT_[ 'error' ] = self::error( __CLASS__, __METHOD__, __LINE__, "An error occured while sending the email, try later.", [ 'result' => $account_row, 'email' => $email ] );}
				} else { $RESULT_[ 'error' ] = self::error( __CLASS__, __METHOD__, __LINE__, "Impossible to read the information in the database, please, try later.", [ 'result' => $account_row, 'email' => $email ] );}
			} else { $RESULT_[ 'error' ] = self::error( __CLASS__, __METHOD__, __LINE__, "We haven't found your email in the database.", [ 'email' => $email ] );}
		} else { $RESULT_[ 'error' ] = self::error( __CLASS__, __METHOD__, __LINE__, "The email to confirm is not valide.", [ 'email' => $email ] );}

		return $RESULT_;
	}
}
