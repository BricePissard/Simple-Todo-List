<?php include_once ( str_replace( 'api/account', 'config' , __DIR__ ) . '/APIHandler.php' );
/**
 * Received from Logger page: /account/forgot
 * Through the JS method stripe.on_logger_form_SUBMIT();
 * @link http://stripe.robby.ai/api/account/forgot.json?key=1234&email=robby.assistant@gmail.com
 *
 * $_POST[ 'key'   ]; // {string}  OAuth2 API KEY.
 * $_POST[ 'email' ]; // {string} email address.
 */
API_result(
	\Stripe\Account_controller::get_forgot(
		( ( isset( $email ) ) ? urldecode( $email ) : NULL )
	),
  ( ( isset( $output ) && $output == \Stripe\Model::API_OUTPUT_XML ) ? \Stripe\Model::API_OUTPUT_XML : \Stripe\Model::API_OUTPUT_JSON )
);
