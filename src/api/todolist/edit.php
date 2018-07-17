<?php include_once ( str_replace( 'api/todolist', 'config' , __DIR__ ) . '/APIHandler.php' );
/**
 *	Received from robby.ai Popup: /popups/commons/Popup_confirm.php
 *  through the JS method: stripe.todolist.save_todo_edited();
 *
 *  $_POST[ 'key'  ];
 *  $_POST[ 'id'   ];
 *  $_POST[ 'name' ];
 */
API_result(
	\Stripe\Todolist_controller::edit([
		'id' 		=> ( ( isset( $id   ) && strlen( $id 	 ) > 0 ) ? $id   : NULL ),
		'name'	=> ( ( isset( $name ) && strlen( $name ) > 0 ) ? $name : NULL ),
	]),
  ( ( isset( $output ) && $output == \Stripe\Model::API_OUTPUT_XML ) ? \Stripe\Model::API_OUTPUT_XML : \Stripe\Model::API_OUTPUT_JSON )
);
