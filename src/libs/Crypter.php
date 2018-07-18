<?php if(!AUTHORIZED){die("Hacking Attempt: ". $_SERVER['REMOTE_ADDR']);}
abstract class Crypter
{
  private static $scramble1 = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';     // 1st string of ASCII characters
  private static $scramble2 = 'AEIOU213579468BPFVDTSZGKCJHQLRMNWY0X';     // 2nd string of ASCII characters

  public static $errors = []; // array of error messages
  private static $adj	= 1.75;	// 1st adjustment value (optional)
  private static $mod = 3;	// 2nd adjustment value (optional)

  public function __construct() {}

	public static function createUniqueKey($text)
	{
		return md5( uniqid( $text.rand(), true ) );
	}

	public static function simple_encrypt($string, $key)
	{
		$result = '';
		for ( $i=1 ; $i<=strlen( $string ) ; $i++ ) {
			$char = substr($string, $i-1, 1);
			$keychar = substr($key, ($i % strlen($key))-1, 1);
			$char = chr(ord($char)+ord($keychar));
			$result .= $char;
		}
		return $result;
	}

	public static function simple_decrypt($string, $key)
	{
		$result = '';
		for ( $i=1 ; $i<=strlen( $string ) ; $i++ ) {
			$char = substr($string, $i-1, 1);
			$keychar = substr($key, ($i % strlen($key))-1, 1);
			$char = chr(ord($char)-ord($keychar));
			$result .= $char;
		}
		return $result;
	}

	/**
	 *	Use to encode account PASSWORD
	 */
	public static function STRING_encrypt($text, $key)
	{
		return str_replace(['+','/'], [',','-'],
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
   *  Use to decode account PASSWORD
   */
	public static function STRING_decrypt($text, $key)
	{
		return openssl_decrypt(
			base64_decode(str_replace([',', '-'], ['+', '/'], $text)),
			"AES-256-CBC",
			hash( 'sha256', $key ),
			TRUE,
			substr( hash( 'sha256', $key ),0,16 )
		);
	}

	/**
	 *	Use to encode accountID
	 */
	public static function NUMBER_encrypt($number, $len=48)
	{
    return self::encrypt('1234567890', strtoupper($number), $len);
	}

	public static function NUMBER_decrypt($number)
	{
    return intval(self::decrypt('1234567890', $number));
	}

 	/**
   * Decrypt string into its original form
   */
  public static function decrypt($key, $source)
  {
    self::$errors = [];
    $fudgefactor = self::_convertKey($key);
		if (isset(self::$errors) && !empty(self::$errors)) {
      return;
    }
    if ( empty( $source ) ) {
      self::$errors[] = 'No value has been supplied for decryption';
      return;
    }
    $target = null;
    $factor2 = 0;
    for ($i = 0; $i < strlen( $source ); $i++) {
      $char2 = substr( $source, $i, 1 );
      $num2 = strpos( self::$scramble2, $char2);
			if ( $num2 === false ) {
        self::$errors[] = "Source string contains an invalid character ($char2)";
        return;
      }
      $adj = self::_applyFudgeFactor($fudgefactor);
      $factor1 = $factor2 + $adj;
      $num1 = $num2 - round($factor1);
      $num1 = self::_checkRange($num1);
      $factor2 = $factor1 + $num2;
      $char1 = substr( self::$scramble1, $num1, 1);
      $target .= $char1;
    }
    return rtrim( $target );
  }


	/**
  * Encrypt string into a garbled form
  */
  public static function encrypt( $key, $source, $sourcelen = 32 )
  {
    self::$errors = [];
    $fudgefactor = self::_convertKey( $key );
    if ( isset( self::$errors ) && !empty( self::$errors ) ) {
			return;
    }
		if ( empty( $source ) ) {
      self::$errors[] = 'No value has been supplied for encryption';
      return;
    }
    while ( strlen( $source ) < $sourcelen ) {
      $source .= 'A';
    }
    $target = null;
    $factor2 = 0;
    for ( $i=0 ; $i < strlen( $source ) ; $i++ ) {
      $char1 = substr( $source, $i, 1 );
      $num1 = strpos( self::$scramble1, $char1 );
	    if ( $num1 === false ) {
        self::$errors[] = "Source string contains an invalid character ($char1)";
        return;
      }
      $adj     	= self::_applyFudgeFactor( $fudgefactor );
      $factor1 	= $factor2 + $adj;
      $num2    	= round( $factor1 ) + $num1;
      $num2    	= self::_checkRange($num2);
      $factor2 	= $factor1 + $num2;
      $char2 		= substr( self::$scramble2, $num2, 1);
      $target .= $char2;
    }
    return $target;
  }

  private static function _applyFudgeFactor (&$fudgefactor)
  {
    $fudge = array_shift( $fudgefactor );
    $fudge = $fudge + self::$adj;
    $fudgefactor[] = $fudge;
    if ( !empty( self::$mod ) ) {
      if ($fudge % self::$mod == 0) {
        $fudge = $fudge * -1;
      }
    }
    return $fudge;
  }

	/**
   * check that $num points to an entry in self::$scramble1
	 * @access private
   */
  private static function _checkRange($num)
  {
    $num = round( $num );
    $limit = strlen( self::$scramble1 );
    while ( $num >= $limit )
    $num = $num - $limit;
    while ( $num<0 )
    $num = $num + $limit;
    return $num;
  }

	/**
   * Convert $key into an array of numbers
	 * @access private
   */
  private static function _convertKey($key)
  {
    if ( empty($key)) {
      self::$errors[] = 'No value has been supplied for the encryption key';
      return;
    }
    $array[] = strlen( $key );
    $tot = 0;
		for ($i=0; $i<strlen($key); $i++) {
      $char = substr( $key, $i, 1 );
      $num = strpos( self::$scramble1, $char );
			if ( $num === false ) {
        self::$errors[] = "Key contains an invalid character ($char)";
        return;
      }
      $array[] = $num;
      $tot = $tot + $num;
    }
    $array[] = $tot;
    return $array;
  }
}
