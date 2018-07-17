<?php if(!AUTHORIZED){die("Hacking Attempt: ". $_SERVER['REMOTE_ADDR']);}

// ******************************************************************************
// A reversible password encryption routine by:
// Copyright 2003-2007 by A J Marston <http://www.tonymarston.net>
// Distributed under the GNU General Public Licence
// Modification: May 2007, M. Kolar <http://mkolar.org>:
// No need for repeating the first character of scramble strings at the end;
// instead using the exact inverse function transforming $num2 to $num1.
// ******************************************************************************

abstract class Crypter
{
  private static $scramble1 = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';     // 1st string of ASCII characters
  private static $scramble2 = 'AEIOU213579468BPFVDTSZGKCJHQLRMNWY0X';     // 2nd string of ASCII characters

  public static $errors 	= []; 	// array of error messages
  private static $adj		= 1.75;	// 1st adjustment value (optional)
  private static $mod 	= 3;	// 2nd adjustment value (optional)

  public function __construct() {}

	public static function createUniqueKey( $text )
	{
		return md5( uniqid( $text.rand(), true ) );
	}

	public static function simple_encrypt( $string, $key )
	{
		$result = '';

		for ( $i=1 ; $i<=strlen( $string ) ; $i++ )
		{
			$char 	 = substr($string, $i-1, 1);
			$keychar = substr($key, ($i % strlen($key))-1, 1);
			$char 	 = chr(ord($char)+ord($keychar));
			$result	.= $char;
		}
		return $result;
	}

	public static function simple_decrypt( $string, $key )
	{
		$result = '';

		for ( $i=1 ; $i<=strlen( $string ) ; $i++ )
		{
			$char 	 = substr($string, $i-1, 1);
			$keychar = substr($key, ($i % strlen($key))-1, 1);
			$char 	 = chr(ord($char)-ord($keychar));
			$result .= $char;
		}
		return $result;
	}



	/**
	 *	Use to encode account EMAIL and PASSWORDs
	 */
	public static function STRING_encrypt( $text, $key )
	{
		return str_replace(
			array('+', '/'),
			array(',', '-'),
			base64_encode(
				openssl_encrypt(
					$text,
					"AES-256-CBC",
					hash( 'sha256', $key ),
					TRUE,
					substr( hash( 'sha256', $key ),0,16 )
				)
			)
		);
  	}
    /**
     *  Use to decode account EMAIL and PASSWORDs
     */
	public static function STRING_decrypt( $text, $key )
	{
		return openssl_decrypt(
			base64_decode(
				str_replace(
					array(',', '-'),
					array('+', '/'),
					$text
				)
			),
			"AES-256-CBC",
			hash( 'sha256', $key ),
			TRUE,
			substr( hash( 'sha256', $key ),0,16 )
		);
	}

	/**
	 *	Use to encode accountID
	 */
	public static function NUMBER_encrypt( $number, $len=48 )
	{
    return self::encrypt( '1234567890', strtoupper( $number ), $len );
	}

	public static function NUMBER_decrypt( $number )
	{
    return intval( self::decrypt( '1234567890', $number ) );
	}




 	// -- decrypt string into its original form
  public static function decrypt( $key, $source )
  {
    self::$errors = [];

    // -- convert $key into a sequence of numbers
    $fudgefactor = self::_convertKey( $key );
    //d( $fudgefactor, self::$errors );

		if ( isset( self::$errors ) && !empty( self::$errors ) )
    {
      return;
    }

    if ( empty( $source ) )
		{
      self::$errors[] = 'No value has been supplied for decryption';
      return;
    }

    $target = null;
    $factor2 = 0;

    for ( $i = 0 ; $i < strlen( $source ) ; $i++ )
		{
      // -- extract a character from $source
      $char2 = substr( $source, $i, 1 );
      // -- identify its position in $scramble2
      $num2 = strpos( self::$scramble2, $char2);

			if ( $num2 === false )
			{
        self::$errors[] = "Source string contains an invalid character ($char2)";
        return;
      }
      // -- get an adjustment value using $fudgefactor
      $adj     = self::_applyFudgeFactor( $fudgefactor );
      $factor1 = $factor2 + $adj;              // accumulate in $factor1
      $num1    = $num2 - round( $factor1 );    // generate offset for $scramble1
      $num1    = self::_checkRange($num1);     // check range
      $factor2 = $factor1 + $num2;             // accumulate in $factor2
      // -- extract character from $scramble1
      $char1 = substr( self::$scramble1, $num1, 1);
      // -- append to $target string
      $target .= $char1;
      //echo "char1=$char1, num1=$num1, adj= $adj, factor1= $factor1, num2=$num2, char2=$char2, factor2= $factor2<br />\n";
    }
    return rtrim( $target );
  }


	// -- encrypt string into a garbled form
  public static function encrypt( $key, $source, $sourcelen = 32 )
  {
    self::$errors = [];

    // -- convert $key into a sequence of numbers
    $fudgefactor = self::_convertKey( $key );
		//d( $fudgefactor, self::$errors );

    if ( isset( self::$errors ) && !empty( self::$errors ) )
    {
			return;
    }

		if ( empty( $source ) )
		{
      self::$errors[] = 'No value has been supplied for encryption';
      return;
    }

    // pad $source with spaces up to $sourcelen
    while ( strlen( $source ) < $sourcelen )
    {
      $source .= 'A';
    }

    $target = null;
    $factor2 = 0;

    for ( $i=0 ; $i < strlen( $source ) ; $i++ )
		{
      // extract a character from $source
      $char1 = substr( $source, $i, 1 );

      // identify its position in $scramble1
      $num1 = strpos( self::$scramble1, $char1 );

	    if ( $num1 === false )
			{
        self::$errors[] = "Source string contains an invalid character ($char1)";
        return;
      }

      // get an adjustment value using $fudgefactor
      $adj     	= self::_applyFudgeFactor( $fudgefactor );
      $factor1 	= $factor2 + $adj;            	// accumulate in $factor1
      $num2    	= round( $factor1 ) + $num1;  	// generate offset for $scramble2
      $num2    	= self::_checkRange($num2);   	// check range
      $factor2 	= $factor1 + $num2;            	// accumulate in $factor2
      $char2 		= substr( self::$scramble2, $num2, 1);
      $target .= $char2;

      //echo "char1=$char1, num1=$num1, adj= $adj, factor1= $factor1, num2=$num2, char2=$char2, factor2= $factor2<br />\n";
    }
    return $target;
  }



  private static function _applyFudgeFactor (&$fudgefactor)
  {
    $fudge = array_shift( $fudgefactor );   // extract 1st number from array
    $fudge = $fudge + self::$adj;           // add in adjustment value
    $fudgefactor[] = $fudge;                // put it back at end of array

    if ( !empty( self::$mod ) )   			    // if modifier has been supplied
		{
      if ($fudge % self::$mod == 0)   	    // if it is divisible by modifier
			{
        $fudge = $fudge * -1;               // make it negative
      }
    }
    return $fudge;
  }

	// -- check that $num points to an entry in self::$scramble1
  private static function _checkRange ( $num )
  {
    $num = round( $num );         			// round up to nearest whole number
    $limit = strlen( self::$scramble1 );

    while ( $num >= $limit )
    $num = $num - $limit;   			// value too high, so reduce it

    while ( $num<0 )
    $num = $num + $limit;   			// value too low, so increase it

    return $num;
  }

	// -- convert $key into an array of numbers
  private static function _convertKey( $key )
  {
    if ( empty( $key ) )
		{
      self::$errors[] = 'No value has been supplied for the encryption key';
      return;
    }

    $array[] = strlen( $key );    // first entry in array is length of $key
    $tot = 0;

		for ( $i = 0 ; $i < strlen( $key ) ; $i++ )
		{
      // extract a character from $key
      $char = substr( $key, $i, 1 );

      // identify its position in $scramble1
      $num = strpos( self::$scramble1, $char );

			if ( $num === false )
			{
        self::$errors[] = "Key contains an invalid character ($char)";
        return;
      }

      $array[] = $num;        // store in output array
      $tot = $tot + $num;     // accumulate total for later
    }

    $array[] = $tot;            // insert total as last entry in array
    return $array;
  }



}
