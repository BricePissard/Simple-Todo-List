<?php include_once ( str_replace( 'api/account', 'config' , __DIR__ ) .  '/APIHandler.php' );
/**
 *	Received from robby.ai Logger page: /acount/logout
 *
 *  $_GET['key']; // = IP
 * 	$_GET['redirect'] =
 */
API_result(
	\Stripe\Account_controller::get_logout(
	   ( isset( $redirect ) && strtoupper( $redirect ) == 'FALSE' ) ? FALSE : TRUE
    ),
	( ( isset( $output ) && $output == \Stripe\Model::API_OUTPUT_XML ) ? \Stripe\Model::API_OUTPUT_XML : \Stripe\Model::API_OUTPUT_JSON )
);
