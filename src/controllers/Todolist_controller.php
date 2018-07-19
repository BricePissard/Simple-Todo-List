<?php
namespace Stripe;

if (!AUTHORIZED) {
	die("Hacking Attempt: ". $_SERVER['REMOTE_ADDR']);
}

final class Todolist_controller extends \Stripe\Controller
{
	/**
	 * Get the list of current Active todos.
	 * The user MUST be logged-in to use this method.
	 *
	 * @access public
	 * @return {array}
	 */
	public static function get_list()
	{
		$RESULT_ = [];
		if ( isset( $_SESSION['ACCOUNT']['id'] ) && intval( $_SESSION['ACCOUNT']['id'] ) > 0 ) {
			$RESULT_ = \Stripe\Todolist_model::read([
				'accountId' => $_SESSION['ACCOUNT']['id'],
				'state'	=> \Stripe\Todolist_model::STATE_ACTIVE
			]);
		} else { $RESULT_['error'] = self::error( __CLASS__, __METHOD__, __LINE__, "You must be logged-in to use this service." );}
		return $RESULT_;
	}

	/**
	 * Add a Todo to the list.
	 * The user MUST be logged-in to use this method.
	 *
	 * @access public
	 * @param {array} with the attributes: 'name': name of the todo to insert.
	 * @return {array}
	 */
	public static function add( Array $data_ = [] )
	{
		$RESULT_ = [];
		if ( isset( $_SESSION['ACCOUNT']['id'] ) && intval( $_SESSION['ACCOUNT']['id'] ) > 0 ) {
			if ( isset( $data_['name'] ) && strlen( $data_['name'] ) > 0 ) {
				$count = \Stripe\Todolist_model::count([
					'accountId' => $_SESSION['ACCOUNT']['id'],
					'state' => \Stripe\Todolist_model::STATE_ACTIVE
				]);
				$id = \Stripe\Todolist_model::create([
					'accountId' => $_SESSION['ACCOUNT']['id'],
					'name' => $data_['name'],
					'position'=> ((isset($count ) && !empty( $count))?$count+1 : 1 )
				]);
				if ( isset( $id ) && $id > 0 ) {
					$RESULT_['id'] = $id;
				} else { $RESULT_['error'] = self::error( __CLASS__, __METHOD__, __LINE__, "Whoops, en error occured while saving the todolist, please try later.", ['id' => $id ] );}
			} else { $RESULT_['error'] = self::error( __CLASS__, __METHOD__, __LINE__, "The todo name is not valid.", $data_ );}
		} else { $RESULT_['error'] = self::error( __CLASS__, __METHOD__, __LINE__, "You must be logged-in to use this service.", $data_ );}

		return $RESULT_;
	}

	/**
	 * Delete a Todo from the list.
	 * The user MUST be logged-in to use this method.
	 *
	 * @access public
	 * @param {array} with the attributes:
	 *									'id': id of the todo to insert, refers to the DB table field `todolist`.`id`
	 * @return {array}
	 */
	public static function delete( Array $data_ = [] )
	{
		$RESULT_ = [];
		if ( isset( $_SESSION['ACCOUNT']['id'] ) && intval( $_SESSION['ACCOUNT']['id'] ) > 0 ) {
			if ( isset( $data_['id'] ) && intval( $data_['id'] ) > 0 ) {
				$return = \Stripe\Todolist_model::delete([
					'accountId' => $_SESSION['ACCOUNT']['id'],
					'id' => $data_['id'],
				]);
				if ( $return === TRUE ) {
					$RESULT_['result'] = 'OK';
				} else { $RESULT_['error'] = self::error( __CLASS__, __METHOD__, __LINE__, "Whoops, en error occured while saving the todolist, please try later.", ['id' => $id ] );}
			} else { $RESULT_['error'] = self::error( __CLASS__, __METHOD__, __LINE__, "The todo ID is not valid, please try later", $data_ );}
		} else { $RESULT_['error'] = self::error( __CLASS__, __METHOD__, __LINE__, "You must be logged-in to use this service.", $data_ );}

		return $RESULT_;
	}

	/**
	 * Change a Todo status, can take the values 'TODO' or 'DONE'.
	 * The user MUST be logged-in to use this method.
	 *
	 * @access public
	 * @param {array} with the attributes:
	 *									'status': status of the todo to modify.
	 *									'id': id of the todo to insert, refers to the DB table field `todolist`.`id`
	 * @return {array}
	 */
	public static function status( Array $data_ = [] )
	{
		$RESULT_ = [];

		if ( isset( $_SESSION['ACCOUNT']['id'] ) && intval( $_SESSION['ACCOUNT']['id'] ) > 0 ) {
			if ( isset( $data_['id'] ) && intval( $data_['id'] ) > 0 ) {
				if ( isset( $data_['status'] ) && strlen( $data_['status'] ) > 0 && (
					strtoupper( $data_['status'] ) == \Stripe\Todolist_model::STATUS_DONE ||
					strtoupper( $data_['status'] ) == \Stripe\Todolist_model::STATUS_TODO
				) ) {
					$id = \Stripe\Todolist_model::create([
						'accountId' => $_SESSION['ACCOUNT']['id'],
						'id' => $data_['id'],
						'status' => strtoupper( $data_['status'] )
					]);
					if ( isset( $id ) && $id > 0 ) {
						$RESULT_['id'] = $id;
					} else { $RESULT_['error'] = self::error( __CLASS__, __METHOD__, __LINE__, "Whoops, en error occured while saving the todolist, please try later.", ['id' => $id ] );}
				} else { $RESULT_['error'] = self::error( __CLASS__, __METHOD__, __LINE__, "The todo status is not valid, please try later", $data_ );}
			} else { $RESULT_['error'] = self::error( __CLASS__, __METHOD__, __LINE__, "The todo ID is not valid, please try later", $data_ );}
		} else { $RESULT_['error'] = self::error( __CLASS__, __METHOD__, __LINE__, "You must be logged-in to use this service.", $data_ );}

		return $RESULT_;
	}

	/**
	 * Change the name of a Todo.
	 * The user MUST be logged-in to use this method.
	 *
	 * @access public
	 * @param {array} with the attributes:
	 *									'name': new name of the todo.
	 *									'id': id of the todo to insert, refers to the DB table field `todolist`.`id`
	 * @return {array}
	 */
	public static function edit( Array $data_ = [] )
	{
		$RESULT_ = [];

		if ( isset( $_SESSION['ACCOUNT']['id'] ) && intval( $_SESSION['ACCOUNT']['id'] ) > 0 ) {
			if ( isset( $data_['id'] ) && intval( $data_['id'] ) > 0 ) {
				if ( isset( $data_['name'] ) && strlen( $data_['name'] ) > 0 ) {
					$id = \Stripe\Todolist_model::create([
						'accountId' => $_SESSION['ACCOUNT']['id'],
						'id' => $data_['id'],
						'name' => $data_['name']
					]);
					if ( isset( $id ) && $id > 0 ) {
						$RESULT_['id'] = $id;
					} else { $RESULT_['error'] = self::error( __CLASS__, __METHOD__, __LINE__, "Whoops, en error occured while saving the todolist, please try later.".$id, ['id' => $id ] );}
				} else { $RESULT_['error'] = self::error( __CLASS__, __METHOD__, __LINE__, "The todo name is not valid, please try later", $data_ );}
			} else { $RESULT_['error'] = self::error( __CLASS__, __METHOD__, __LINE__, "The todo ID is not valid, please try later", $data_ );}
		} else { $RESULT_['error'] = self::error( __CLASS__, __METHOD__, __LINE__, "You must be logged-in to use this service.", $data_ );}

		return $RESULT_;
	}

	/**
	 * Reorder all the ACTIVE Todos positions for this account.
	 * The user MUST be logged-in to use this method.
	 *
	 * @access public
	 * @param {array} with the attributes:
	 *									'positions': {array} Array of coupled .
	 *									'id': {int} id of the todo to insert, refers to the DB table field `todolist`.`id`
	 * @return {array}
	 */
	public static function positions( Array $data_ = [] )
	{
		$RESULT_ = [];
		if ( isset( $_SESSION['ACCOUNT']['id'] ) && intval( $_SESSION['ACCOUNT']['id'] ) > 0 ) {
			if ( isset( $data_['positions'] ) && !empty( $data_['positions'] ) ) {
				$result = \Stripe\Todolist_model::positions([
					'positions' => $data_['positions']
				]);
				if ( $result == TRUE ) {
					$RESULT_['result'] = 'OK';
				} else { $RESULT_['error'] = self::error( __CLASS__, __METHOD__, __LINE__, "Whoops, en error occured while saving the todolist, please try later.".$result, ['result' => $result ] );}
			} else { $RESULT_['error'] = self::error( __CLASS__, __METHOD__, __LINE__, "The array of positions is not valid, please try later", $data_ );}
		} else { $RESULT_['error'] = self::error( __CLASS__, __METHOD__, __LINE__, "You must be logged-in to use this service.", $data_ );}

		return $RESULT_;
	}

}
