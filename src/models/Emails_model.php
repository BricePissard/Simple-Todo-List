<?php
namespace Stripe;

if ( !AUTHORIZED ){
	die( "Hacking Attempt: ". $_SERVER[ 'REMOTE_ADDR' ] );
}

final class Emails_model extends \Stripe\Model
{
	public function __construct() {}

	public static function send( $to, $subject, $message, $from = NULL, $from_name = NULL, $file_name = '', $file_data = NULL, $file_type = 'application/octet-stream' )
	{
		if ( class_exists( 'PHPmailer' ) ) {
			$from 		 = ( ( !isset( $from      ) || empty( $from      ) ) ? 'noreply@robby.ai' : $from );
			$from_name = ( ( !isset( $from_name ) || empty( $from_name ) ) ? 'noreply@robby.ai' : $from_name );

			$mail = new \PHPmailer();
      $mail->IsSendmail();
      $mail->IsHTML( TRUE );
			$mail->WordWrap = 50; // set word wrap
			$mail->CharSet  = "UTF-8";
			$mail->AddCustomHeader( "X-Mailer: Mailinglist v1.71");
			$mail->AddCustomHeader( "X-MessageID: TodoList-Mailer-" . $mail->Username );
			$mail->AddCustomHeader( "X-ListMember: " . $mail->Username );
			$mail->AddCustomHeader( "Precedence: bulk");

      $mail->ClearAllRecipients(); // <-- important
      $mail->AddReplyTo( $from );
      $mail->From =      $from;
      $mail->FromName =  $from_name;
			$mail->AddAddress( $to );

			$mail->ClearAttachments();
			if ( strlen( $file_name ) > 0 && $file_data != NULL ) {
			  $mail->AddStringAttachment( $file_data, $file_name, 'base64', $file_type );
      }

      $mail->Subject = $subject;
      $mail->Body    = $message;

      $isSent = $mail->send();

			return TRUE;
		}
	}


}
