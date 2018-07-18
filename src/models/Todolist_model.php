<?php
namespace Stripe;

if (!AUTHORIZED) {
  die("Hacking Attempt [CategoriesModel] : ". $_SERVER['REMOTE_ADDR']);
}

/**
 * Database structure:
 *
 * CREATE TABLE `todolist` (
 *   `id`            bigint(15)               UNSIGNED NOT NULL COMMENT 'Unique Todo identifier',
 *   `accountId`     bigint(15)               UNSIGNED DEFAULT NULL COMMENT 'Refers to the database field `account`.`id`',
 *   `name`          varchar(512)             COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Name of the Todo',
 *   `position`      bigint(15)               UNSIGNED DEFAULT NULL COMMENT 'Ordering position of the Todo in ASC (smaller on the top of the list) ',
 *   `status`        enum('DONE','TODO')      COLLATE utf8_unicode_ci NOT NULL DEFAULT 'TODO',
 *   `state`         enum('ACTIVE','DELETE')  COLLATE utf8_unicode_ci NOT NULL DEFAULT 'ACTIVE',
 *   `date_created`  timestamp                NOT NULL DEFAULT CURRENT_TIMESTAMP,
 *   `date_updated`  timestamp                NOT NULL ON UPDATE CURRENT_TIMESTAMP
 * ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
 *
 * ALTER TABLE `todolist`
 *   ADD PRIMARY KEY      (`id`),
 *   ADD KEY `name`       (`name`(255)),
 *   ADD KEY `status`     (`status`),
 *   ADD KEY `state`      (`state`),
 *   ADD KEY `accountId`  (`accountId`);
 *
 * ALTER TABLE `todolist`
 *   MODIFY `id` bigint(15) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Unique Todo identifier';
 *
 * ALTER TABLE `todolist`
 *   ADD CONSTRAINT `accountId` FOREIGN KEY (`accountId`) REFERENCES `account` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
 *   COMMIT;
 */
final class Todolist_model extends \Stripe\Model implements \Stripe\iCRUDS
{
  const TABLE = 'todolist';
  const STATE_ACTIVE 	= 'ACTIVE';
	const STATE_DELETED	= 'DELETE';
  const STATUS_DONE = 'DONE';
	const STATUS_TODO	= 'TODO';

  public static $sql = NULL;

  public static function create( $_data )
  {
    if (
	   isset(  $_data[ 'accountId' ] ) &&
     !empty( $_data[ 'accountId' ] ) &&
     intval( $_data[ 'accountId' ] ) > 0
    ) {
      self::$sql =
      " INSERT IGNORE INTO ". DB_BASE . "." . self::TABLE .
      " ( ".
       	( ( !isset(  $_data[ 'id'        ] ) ) ? '' : "id," 			 )  .
        ( ( !isset(  $_data[ 'accountId' ] ) ) ? '' : "accountId," )  .
        ( ( !isset(  $_data[ 'name'      ] ) ) ? '' : "name,"      )  .
        ( ( !isset(  $_data[ 'position'  ] ) ) ? '' : "position,"  )  .
        ( ( !isset(  $_data[ 'status'    ] ) ) ? '' : "status,"    )  .
        ( ( !isset(  $_data[ 'state'     ] ) ) ? '' : "state,"     )  .
          												 	                  "date_created," .
                                                      "date_updated"  .
      " ) ".
      " VALUES ".
      " ( ".
      	( ( !isset( $_data[ 'id'        ] ) ) ? '' : 		   intval(		           	$_data[ 'id'        ] ) . ", " ) .
        ( ( !isset( $_data[ 'accountId' ] ) ) ? '' :       intval(                $_data[ 'accountId' ] ) . ", " ) .
        ( ( !isset( $_data[ 'name'      ] ) ) ? '' : "'" . \Strings::DBTextClean( $_data[ 'name'      ]	) . "'," ) .
        ( ( !isset( $_data[ 'position'  ] ) ) ? '' :       intval(                $_data[ 'position'  ] ) . ", " ) .
        ( ( !isset( $_data[ 'status'    ] ) ) ? '' : "'" . \Strings::DBTextClean( $_data[ 'status'    ]	) . "'," ) .
        ( ( !isset( $_data[ 'state'     ] ) ) ? '' : "'" . \Strings::DBTextClean( $_data[ 'state'     ]	) . "'," ) .
              									 		 		             "'" . \Strings::DBCurrentDate()						  			  . "',"   .
                                                     "'" . \Strings::DBCurrentDate()						  			  . "' "   .
      " ) " .
      ( ( isset( $_data[ 'id' ] ) && intval( $_data[ 'id' ] ) > 0 ) ?
      " ON DUPLICATE KEY UPDATE ".
        ( ( !isset( $_data[ 'accountId'    ] ) ) ? '' : "accountId = '" .    intval(                $_data[ 'accountId' ] ) . "'," ) .
      	( ( !isset( $_data[ 'name'         ] ) ) ? '' : "name = '" .         \Strings::DBTextClean( $_data[ 'name'      ] ) . "'," ) .
      	( ( !isset( $_data[ 'status'       ] ) ) ? '' : "status = '" .       \Strings::DBTextClean( $_data[ 'status'    ] ) . "'," ) .
      	( ( !isset( $_data[ 'state'        ] ) ) ? '' : "state = '" .        \Strings::DBTextClean( $_data[ 'state'     ] ) . "'," ) .
      	( ( !isset( $_data[ 'position'     ] ) ) ? '' : "position = '" .     intval(                $_data[ 'position'  ] ) . "'," ) .
        ( ( !isset( $_data[ 'date_updated' ] ) ) ? '' : "date_updated = '" . \Strings::DBCurrentDate()                      . "'," ) .
        "id = " . ( ( isset( $_data[ 'id' ] ) && intval( $_data[ 'id' ] ) > 0 ) ? intval( $_data[ 'id' ] ) : "LAST_INSERT_ID( id )" ) . ";"
        : ';'
      );
		  $DB = \Stripe\Model::$DB;
      $DB = ( ( !$DB ) ? \Stripe\Model::get_db() : $DB );
      if ( is_callable([ $DB, 'prepare' ]) ) {
        try {
          $query = $DB->prepare( self::$sql );
          if ( !$query ) { self::error( __CLASS__, __METHOD__, self::$sql, ( is_callable([ $DB, 'errorInfo' ]) === TRUE ) ? $DB->errorInfo() : '' ); }
    			if ( $query->execute() ) {
            return intval( ( isset( $_data[ 'id' ] ) && intval( $_data[ 'id' ] ) > 0 ) ?
              $_data[ 'id' ]
              :
              $DB->lastInsertId()
            );
          } else { \Stripe\Controller::error( __CLASS__, __METHOD__, __LINE__, "SQL Error", [ "sql" => self::$sql, "error" => ( is_callable([ $DB, 'errorInfo' ]) === TRUE ) ? $DB->errorInfo() : '' ], TRUE, NULL ); }
        } catch ( \PDOException $err ) {
          \Stripe\Controller::error( __CLASS__, __METHOD__, __LINE__, $err->getMessage(), self::$sql, TRUE, $err );
        }
      }
    }
    return -1;
  }

  public static function read( $_data )
  {
    $_a = [];
    if ( isset( $_data[ 'accountId' ] ) && intval( $_data[ 'accountId' ] ) > 0 ) {
      self::$sql = "".
  	  " SELECT * ".
   	  " FROM " . DB_BASE . "." . self::TABLE .
   	  " WHERE accountId = '". intval( $_data[ 'accountId' ] ) . "' ".
      " AND state = '" . \Strings::DBTextClean( $_data[ 'state' ] ) . "' " .
      " ORDER BY position, id ASC;";
  	  if ( is_callable([ self::$DB, 'prepare' ]) ) {
  	    try {
            $query = self::$DB->prepare( self::$sql );
            if ( !$query) { self::error( __CLASS__, __METHOD__, self::$sql ); }
            $query->execute();
            for ( $i = 0 ; $row = $query->fetch() ; $i++ ) {
              $_a = self::_list( $_a, $row, $i );
            }
          } catch ( \PDOException $err ) {
            \Stripe\Controller::error( __CLASS__, __METHOD__, __LINE__, $err->getMessage(), self::$sql );
          }
  	  }
    }
    return $_a;
  }

  public static function count( $_data )
  {
    $_a = 0;
    if ( isset( $_data[ 'accountId' ] ) && intval( $_data[ 'accountId' ] ) > 0 ) {
      self::$sql = "".
  	  " SELECT COUNT(*) as count".
   	  " FROM " . DB_BASE . "." . self::TABLE .
   	  " WHERE accountId = '". intval( $_data[ 'accountId' ] ) . "' ".
      " AND state = '" . \Strings::DBTextClean( $_data[ 'state' ] ) . "' " .
      ";";
  	  if ( is_callable([ self::$DB, 'prepare' ]) ) {
  	    try {
          $query = self::$DB->prepare( self::$sql );
          if ( !$query) { self::error( __CLASS__, __METHOD__, self::$sql ); }
          $query->execute();
          $result_ = $query->fetch();
          $_a = ( ( isset( $result_[ 'count' ] ) && !empty( $result_[ 'count' ] ) ) ? intval( $result_[ 'count' ] ) : 0 );
        } catch ( \PDOException $err ) {
          \Stripe\Controller::error( __CLASS__, __METHOD__, __LINE__, $err->getMessage(), self::$sql );
        }
  	  }
    }
    return $_a;
  }

  public static function update_positions( $_data )
  {
    $values_ = [];
    foreach( $_data[ 'positions' ] as $pos_ ) {
      array_push( $values_, "(".$pos_['id'].",".$pos_['position'].")" );
    }
    self::$sql =
    " INSERT IGNORE INTO ". DB_BASE . "." . self::TABLE .
      " (id,position) " .
    " VALUES " .
      implode( ',', $values_ ) .
    " ON DUPLICATE KEY UPDATE ".
      " position=VALUES(position)" .
    ";";
    $DB = \Stripe\Model::$DB;
    $DB = ( ( !$DB ) ? \Stripe\Model::get_db() : $DB );
    if ( is_callable([ $DB, 'prepare' ]) ) {
      try {
        $query = $DB->prepare( self::$sql );
        if ( !$query ) { self::error( __CLASS__, __METHOD__, self::$sql, ( is_callable([ $DB, 'errorInfo' ]) === TRUE ) ? $DB->errorInfo() : '' ); }
        if ( $query->execute() ) {
          $result = $query->fetch();
          return TRUE;
        } else { \Stripe\Controller::error( __CLASS__, __METHOD__, __LINE__, "SQL Error", [ "sql" => self::$sql, "error" => ( is_callable([ $DB, 'errorInfo' ]) === TRUE ) ? $DB->errorInfo() : '' ], TRUE, NULL ); }
      } catch ( \PDOException $err ) {
        \Stripe\Controller::error( __CLASS__, __METHOD__, __LINE__, $err->getMessage(), self::$sql, TRUE, $err );
      }
    }
    return TRUE;
  }

  public static function delete( $_data )
  {
    if (
      isset(  $_data[ 'id' ] ) &&
      intval( $_data[ 'id' ] ) > 0
    )
    {
      if (
        isset(  $_data[ 'accountId' ] ) &&
        intval( $_data[ 'accountId' ] ) > 0
      )
      {
        self::$sql =
        " UPDATE ". DB_BASE . "." . self::TABLE .
        " SET state = '" .    self::STATE_DELETED . "' " .
        " WHERE id = " .      intval( $_data[ 'id'        ] ) .
        " AND accountId = " . intval( $_data[ 'accountId' ] ) .
        ";";

        if ( is_callable([ self::$DB, 'prepare' ]) === TRUE )
        {
          try
          {
            $query = self::$DB->prepare( self::$sql );

            if ( !$query ) { self::error( __CLASS__, __METHOD__, self::$sql, ( is_callable([ $DB, 'errorInfo' ]) === TRUE ) ? $DB->errorInfo() : '' ); }
      			if ( $query->execute() === TRUE )
            {
              return TRUE;
            }
            else { self::error( __CLASS__, __METHOD__, self::$sql, ( is_callable([ $DB, 'errorInfo' ]) === TRUE ) ? $DB->errorInfo() : '' ); }
          }
          catch ( \PDOException $err )
          {
            \Stripe\Controller::error( __CLASS__, __METHOD__, __LINE__, $err->getMessage(), self::$sql, TRUE, $err );
          }
        }
      }
    }
    return FALSE;
  }

	public static function search( $name = '' ) {}


  // -- Private methods

	private static function _list($_a, $row, $i=0)
	{
    if ( isset($row) && !empty($row) ) {
      array_push( $_a, [
        'id'            => ( ( isset( $row[ 'id'           ] ) ) ? $row[ 'id'           ] : NULL ),
        'accountId'     => ( ( isset( $row[ 'accountId'    ] ) ) ? $row[ 'accountId'    ] : NULL ),
        'name'          => ( ( isset( $row[ 'name'         ] ) ) ? $row[ 'name'         ] : NULL ),
        'position'      => ( ( isset( $row[ 'position'     ] ) ) ? $row[ 'position'     ] : NULL ),
        'status'        => ( ( isset( $row[ 'status'       ] ) ) ? $row[ 'status'       ] : NULL ),
        'state'         => ( ( isset( $row[ 'state' 		   ] ) ) ? $row[ 'state'        ] : NULL ),
        'date_created'  => ( ( isset( $row[ 'date_created' ] ) ) ? $row[ 'date_created' ] : NULL ),
        'date_updated'  => ( ( isset( $row[ 'date_updated' ] ) ) ? $row[ 'date_updated' ] : NULL )
      ]);
    }
    return $_a;
	}
}
