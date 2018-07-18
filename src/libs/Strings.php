<?php if(!AUTHORIZED){die("Hacking Attempt: ". $_SERVER['REMOTE_ADDR']);}
abstract class Strings
{
	public static function DBTextClean($text)
	{
		return trim( addslashes( htmlspecialchars( $text, ENT_COMPAT, 'UTF-8' ) ) );
	}

	public static function DBTextToWeb($text)
	{
		return nl2br( html_entity_decode( htmlspecialchars_decode( stripslashes( rawurldecode( $text ) ) ), ENT_COMPAT, 'UTF-8' ) );
	}

	public static function stripHTMLtags($text)
	{
		if ( is_string( $text ) ) {
			$search = [
				'@<iframe[^>]*?>.*?</iframe>@si',  	// Strip out javascript
				'@<script[^>]*?>.*?</script>@si',  	// Strip out javascript
				'@<style[^>]*?>.*?</style>@siU',    // Strip style tags properly
				'@<[\/\!]*?[^<>]*?>@si',            // Strip out HTML tags
				'@<![\s\S]*?--[ \t\n\r]*>@'        	// Strip multi-line comments including CDATA
			];
			$text = @preg_replace( $search, '', $text );
		}
		return ( ( is_string( $text ) ) ? strip_tags( $text ) : $text );
	}

	/**
	 * Control if the Email submited is correctly formated (RFC 5321)
	 *
	 * @access	public
	 * @param		{String}	email value to control
	 * @return	{Boolean}
	 */
	public static function isValidEmail($email)
	{
		$email = trim( $email );
		if ( !isset( $email ) || ( isset($email) && strlen($email) <= 3)) {
			return FALSE;
		}
		if ( preg_match( '/^\w[-.\w]*@(\w[-._\w]*\.[a-zA-Z]{2,}.*)$/', $email, $matches ) ) {
    	$hostName = $matches[ 1 ];
			if (strlen( $hostName ) > 5) {
	     	if ( function_exists('checkdnsrr')) {
					if ( checkdnsrr( $hostName . '.', 'MX' ) ) return TRUE;
					if ( checkdnsrr( $hostName . '.', 'A'  ) ) return TRUE;
				} else {
					exec( "nslookup -type=MX ".$hostName, $r );
					if ( count( $r ) > 0 ) {
						foreach ( $r as $line) {
							if ( preg_match("^$hostName", $line)) {
								return TRUE;
							}
						}
						return FALSE;
					} else { return TRUE; }// if a problem occured while resolving the MX consider the email as valid
				}
			}
    } else {
			if ( preg_match( "/^[0-9a-z]([-_.]?[0-9a-z])*@[0-9a-z]([-.]?[0-9a-z])*\\.[a-z]{2,3}$/", $email ) > 0 )
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Get the date formated for the database field.
	 *
	 * @access private
	 * @return {string}
	 */
	public static function DBCurrentDate()
  {
  	return date( TIMESTAMP_STRUCTURE );
  }

	/**
	 * Control if the URL is correctly formated (RFC 3986)
	 * An IP can also be submited as a URL.
	 *
	 * @access	public
	 * @param		{String}	URL string element to control
	 * @return	{Boolean}
	 */
	public static function isValidURL($url='')
	{
		$url = trim( $url );
		$ereg = "/^(http|https|ftp):\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i";
		return ( preg_match( $ereg, $url ) > 0 && strlen( $url ) > 10 ) ? TRUE : self::isValidIP( $url );
	}

	/**
	 * 	Control if the parameter submited is a valide IP v4
	 *
	 * 	@access public
	 * 	@param	{String}	Value of the IP to evaluate
	 * 	@return	{Boolean}	If the parameter submited is a valide IP return TRUE, FALSE else.
	 */
	public static function isValidIP($ip='')
	{
		$ip = trim( $ip );
		$regexp = '/^((1?\d{1,2}|2[0-4]\d|25[0-5])\.){3}(1?\d{1,2}|2[0-4]\d|25[0-5])$/';
		if ( preg_match( $regexp, $ip ) <= 0 ) {
			return FALSE;
		} else {
			$a = explode( ".", $ip );
			if ( $a[0] > 255) { return FALSE; }
			if ( $a[1] > 255) { return FALSE; }
			if ( $a[2] > 255) {	return FALSE; }
			if ( $a[3] > 255) { return FALSE; }
			return TRUE;
		}
		return FALSE;
	}
}
