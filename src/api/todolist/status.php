<?php include_once ( str_replace( 'api/todolist', 'config' , __DIR__ ) . '/APIHandler.php' );
/**
 * Received from robby.ai Popup: /popups/commons/Popup_confirm.php
 * Through the JS method: stripe.todo_delete();
 * @link http://stripe.robby.ai/api/todolist/status.json?key=1234&id=1234&status=DONE
 *
 *  $_POST[ 'key'    ]; // {string} OAuth 2 API Key.
 *  $_POST[ 'id'     ]; // {int} Todo id, refers to the DB table field `todolist`.`id`
 *  $_POST[ 'status' ]; // {string} new status of this todo, can be 'DONE' or 'TODO'.
 */
API_result(
	\Stripe\Todolist_controller::status([
		'id' => ( ( isset( $id ) && strlen( $id ) > 0 ) ? $id : NULL ),
		'status' => ( ( isset( $status ) && strlen( $status ) > 0 ) ? $status : NULL ),
	]),
  ( ( isset( $output ) && $output == \Stripe\Model::API_OUTPUT_XML ) ? \Stripe\Model::API_OUTPUT_XML : \Stripe\Model::API_OUTPUT_JSON )
);
