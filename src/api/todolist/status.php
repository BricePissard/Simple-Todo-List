<?php include_once ( str_replace( 'api/todolist', 'config' , __DIR__ ) . '/APIHandler.php' );
/**
 *	Received from robby.ai Popup: /popups/commons/Popup_confirm.php
 *  through the JS method: stripe.todo_delete();
 *
 *  $_POST[ 'key'    ];
 *  $_POST[ 'id'     ];
 *  $_POST[ 'status' ];
 */
API_result(
	\Stripe\Todolist_controller::status([
		'id' 			=> ( ( isset( $id     ) && strlen( $id 		 ) > 0 ) ? $id     : NULL ),
		'status'	=> ( ( isset( $status ) && strlen( $status ) > 0 ) ? $status : NULL ),
	]),
  ( ( isset( $output ) && $output == \Stripe\Model::API_OUTPUT_XML ) ? \Stripe\Model::API_OUTPUT_XML : \Stripe\Model::API_OUTPUT_JSON )
);
