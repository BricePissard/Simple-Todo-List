<?php include_once ( str_replace( 'api/account', 'config' , __DIR__ ) . '/APIHandler.php' );
/**
 * Received from robby.ai Logger page: /account/signin
 * @link https://stripe.robby.ai/api/account/signin.json?key=1234&redirect=&email=&password=
 *
 * @param key					  {string} IP
 * @param email				  {string} account email
 * @param password			{string} un-encrypted account password
 * @param first_name		{string} [OPTIONAL]
 * @param last_name     {string} [OPTIONAL]
 */
API_result(
	\Stripe\Account_controller::get_signin(
		( ( isset( $redirect ) ) ? $redirect : NULL ),
		( ( isset( $email    ) ) ? $email    : NULL ),
		( ( isset( $password ) ) ? $password : NULL ),
		[
			'first_name' => ( ( isset( $first_name ) ) ? $first_name : NULL ),
			'last_name'	 => ( ( isset( $last_name  ) ) ? $last_name  : NULL )
		]
	),
	( ( isset( $output ) && $output == \Stripe\Model::API_OUTPUT_XML ) ? \Stripe\Model::API_OUTPUT_XML : \Stripe\Model::API_OUTPUT_JSON )
);
