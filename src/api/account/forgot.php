<?php include_once ( str_replace( 'api/account', 'config' , __DIR__ ) . '/APIHandler.php' );
/**
 *	Received from robby.ai Logger page: /account/forgot
 *
 *  $_GET['key']; // = IP
 *  $_POST['email'];
 */
API_result(
	\Stripe\Account_controller::get_forgot(
		( ( isset( $email ) ) ? urldecode( $email ) : NULL )
	),
    ( ( isset( $output ) && $output == \Stripe\Model::API_OUTPUT_XML ) ? \Stripe\Model::API_OUTPUT_XML : \Stripe\Model::API_OUTPUT_JSON )
);
