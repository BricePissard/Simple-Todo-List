<?php include_once ( str_replace( 'api/account', 'config' , __DIR__ ) .  '/APIHandler.php' );
/**
 *	Received from robby.ai.com Logger page: /account/login
 *
 *  $_GET['key']; // = IP
 *  $_POST['email'];
 *  $_POST['password'];
 */
API_result(
	\Stripe\Account_controller::get_login(
		( ( isset( $redirect ) ) ? $redirect : NULL ),
		( ( isset( $email    ) ) ? $email    : NULL ),
		( ( isset( $password ) ) ? $password : NULL )
	),
    ( ( isset( $output ) && $output == \Stripe\Model::API_OUTPUT_XML ) ? \Stripe\Model::API_OUTPUT_XML : \Stripe\Model::API_OUTPUT_JSON )
);
