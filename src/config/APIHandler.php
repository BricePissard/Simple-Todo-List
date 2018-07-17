<?php include_once( dirname( __FILE__ ) . '/config.php' );

// -- Handle all GET and POST vars
if ( isset( $_GET  ) ) { while ( list( $__key, $val ) = each( $_GET  ) ) { if ( isset( $val ) ) { $$__key = $val; } } }
if ( isset( $_POST ) ) { while ( list( $__key, $val ) = each( $_POST ) ) { if ( isset( $val ) ) { $$__key = Strings::stripHTMLtags( $val ); } } } // in libraries.string

// -- Estimate the 'key' value sent either from GET, POST or shell parameter
if      ( isset( $key     ) ) { $key = preg_replace( '/\ /', '+', $key     ); }
else if ( isset( $argv[1] ) ) { $key = preg_replace( '/\ /', '+', $argv[1] ); }

if ( !isset( $key ) ) { API_result([ 'error' => 'No API KEY specified' ]); die; }

$output = ( ( isset( $_GET[ 'output' ] ) ) ? $_GET[ 'output' ] : ( ( isset( $_POST[ 'output' ] ) ) ? $_POST[ 'output' ] : ( ( function_exists( 'pathinfo' ) ) ? pathinfo( ( ( isset( $_SERVER[ 'SCRIPT_URL' ] ) ) ? $_SERVER[ 'SCRIPT_URL' ] : ( ( isset( $_SERVER[ 'REQUEST_URI' ] ) ) ? $_SERVER[ 'REQUEST_URI' ] : '' ) ), PATHINFO_EXTENSION ) : '' ) ) );
$output = ( ( isset( $output )? ( ( strtolower( $output ) == \Stripe\Model::API_OUTPUT_XML ) ? \Stripe\Model::API_OUTPUT_XML : \Stripe\Model::API_OUTPUT_JSON ) : \Stripe\Model::API_OUTPUT_JSON ) );


/**
 * Output API results:
 *
 * @param {array}   $data_
 * @param {string}  $output 'json' | 'xml'
 * @param {boolean} Define if the results have to start with 'result' root handler access.
 *
 * @return {blob}   Print on screen the blob of data with the addoc header.
 */
function API_result( $_data, $output = NULL, $noRoot = FALSE )
{
  $is_header = ( ( isset( $_POST[ 'is_header' ] ) ) ? $_POST[ 'is_header' ] : ( ( DEBUG ) ? FALSE : TRUE ) );

  $output = ( (
    !isset(  $output ) ||
    is_null( $output ) ||
    (
      $output !== \Stripe\Model::API_OUTPUT_JSON &&
      $output !== \Stripe\Model::API_OUTPUT_XML
    ) ) ?
      \Stripe\Model::API_OUTPUT_JSON
      :
      $output
  );

  if ( $is_header === TRUE )
  {
    header( "Access-Control-Allow-Origin: *" );
  }

  $root = 'result';

	if ( isset( $_GET[ 'callback' ] ) )
  {
  	if ( $is_header === TRUE )
    {
      header( "Content-type: application/json; charset=utf-8" );
    }
  	echo $_GET[ 'callback' ] . '(' . json_encode([ $root => (array)$_data ]) . ')';
  }
	else
	{
	  if ( $is_header === TRUE )
    {
      header( "Content-type: application/json; charset=utf-8" );
    }
		echo json_encode( ( ( $noRoot == TRUE )? (array)$_data : [ $root => (array)$_data ] ), PROD ? NULL : JSON_PRETTY_PRINT );
	}
  //die;
}
