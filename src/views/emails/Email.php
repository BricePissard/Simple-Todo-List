<?php
namespace Stripe;

if ( !AUTHORIZED ){
	die( "Hacking Attempt: ". $_SERVER[ 'REMOTE_ADDR' ] );
}

abstract class Email
{
	public static $email 	 = NULL;
	public static $subject = NULL;
	public static $message = NULL;

	public function __construct() {}

	private static function css()
	{
		?><style>
		body {background:#e0e1e5;font-family:'Helvetica Neue',Helvetica,'Lucida Grande',tahoma,verdana,arial,sans-serif;font-weight:400}
		a {color:#3887BE;text-decoration:none;white-space:nowrap !important;cursor:pointer;}
		a, a:link, a:visited, a:active {color:#2398C9;cursor:pointer;text-decoration:none;}
		#logoImg {width:60px;height:60px;}
		#email_table{width:100% !important;}
		#email_content{padding:0 !important;}
		#profile_pic img{border:0;}
		*[class].usercard{background:#f9f9f9;}
		@media all and (max-device-width: 720px){
			a{white-space:pre-wrap !important;}
			table[bgcolor="#e9eaed"]{background:transparent !important;}
			*[id]#body_container{border-bottom:1px solid #e5e5e5 !important;}
			table[width="730"],
			*[id]#body_container,
			*[id]#cta_container{table-layout:fixed;}
			*[id]#cta_outer{border:none !important;}
			*[id]#header_profile > table > tbody > tr > td:not(:nth-child(4)){display:none !important;}
			*[id]#profile_name{display:none;}
			*[id]#profile_pic{-moz-border-radius:3px !important;-webkit-border-radius:3px !important;border-radius:3px !important;border-width:0 !important;overflow:hidden}
			*[id]#header_title{width:auto !important;}
			*[id]#header_profile{width:24px;}
			*[class].bio{display:none !important;}
			*[id]#main_content{width:100%;}
			*[class].content > div a{display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap !important;width:160px}
			*[class].ext{padding-right:20px}
			*[class].image a{display:block;margin-left:20px}
			*[class].cta_btn,
			*[class].scnd_btn{display:block}
			*[id]#email_cta > tbody > tr > td[width="100%"]{display:none}
		}
		@media all and (device-width: 720px){
			table[width="730"],
			*[id]#body_container,
			*[id]#footer_container{width:340px}
			*[class].usercard{width:300px !important}
		}
		@media all and (max-device-width: 480px){
			*[id]#cta_container > table > tbody > tr > td[height="15"]{display:none !important}
		}
		@media all and (device-width: 320px){
			table[width="730"],
			*[id]#body_container,
			*[id]#cta_container{min-width:400px;width:auto}center{padding:0 10px}
			*[class].content > div a{width:182px}
		}
		@media all and (device-width: 720px){
			*[class].expl{width:280px !important}
		}</style><?php
	}

	private static function header()
	{
		?><table id="header" cellspacing="0" cellpadding="0" width="100%" style="background:#47DF9A;border-collapse:collapse;width:100%;box-shadow:0 1px 1px rgba(0,0,0,.25);height:8px;"><?php
			?><tr><td></td></tr><?php
		?></table><?php
	}

	public static function title( $title='' )
	{
		?><table cellspacing="0" cellpadding="0" width="100%" height="60" style="border-collapse:collapse;text-align:center;border-color:#e5e5e5;border-style:solid;border-width:0 0 1px 0;"><?php
			?><tr><td height="4" colspan="3">&nbsp;</td></tr><?php
			?><tr><?php
				?><td width="20"></td><?php
				?><td><?php
					?><table cellspacing="0" cellpadding="0" width="100%" height="40"><?php
						?><tr><?php
							?><td style="vertical-align:middle;width:150px;text-align:center;padding:2px 25px 0 5px;border-right:1px solid #d8d8d9;" align="center"><?php
								?><a href="<?=PROTOCOL.ROOT_DOMAIN;?>"><?php
									?><img id="logoImg" src="<?=\Stripe\Model::get_asset_path( "logo.png", 'IMG', FALSE, TRUE );?>" alt="TodoList App" width="60" height="60"/><?php
								?></a><?php
							?></td><?php
							?><td align="left" style="vertical-align:middle;padding-left:18px;"><?php
								?><span id="headline" style="font-family:Helvetica Neue,Helvetica,Lucida Grande,tahoma,verdana,arial,sans-serif;color:#7f7f7f;font-size:16px;font-style:normal;"><?php
									?><font size="4"><?=$title;?></font><?php
								?></span><?php
							?></td><?php
						?></tr><?php
					?></table><?php
				?></td><?php
				?><td width="20"></td><?php
			?></tr><?php
			?><tr><td height="4" colspan="3">&nbsp;</td></tr><?php
		?></table><?php
	}

	public static function comment( $text = null )
	{
		if ( isset( $text ) )
		{
			?><tr><td height="30" colspan="3" style="height:30px;">&nbsp;</td></tr><?php
			?><tr><?php
				?><td width="10%"></td><?php
				?><td><?php
					?><center><?php
						?><table cellspacing="0" cellpadding="0" width="100%" class="expl" style="border-collapse:collapse;"><?php
							?><tr><?php
								?><td><?php
									?><span class="expl" style="font-family:Helvetica Neue,Helvetica,Lucida Grande,tahoma,verdana,arial,sans-serif;color:#7f7f7f;display:block;font-size:15px;line-height:20px;width:100%;text-align:left;"><?php
										?><font size="3"><?php

											echo $text;

										?></font><?php
									?></span><?php
								?></td><?php
							?></tr><?php
						?></table><?php
					?></center><?php
				?></td><?php
				?><td width="10%"></td><?php
			?></tr><?php
			?><tr><td height="30" colspan="3" style="height:30px;">&nbsp;</td></tr><?php
		}
	}

	public static function in( $email = "" )
	{
		self::$email = $email;

		?><!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Strict //EN" "https://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"><?php
		?><html xmlns="https://www.w3.org/1999/xhtml"><?php
			?><head><?php
				?><title>Todo App</title><?php
				?><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/><?php

				self::css();

			?></head><?php
		?><body style="margin:0;padding:0;position:relative;background-color:#eaeaea;" dir="ltr"><?php
			?><table cellspacing="0" cellpadding="0" id="email_table" style="border-collapse:collapse;width:100%;" border="0"><?php
				?><tr><?php
					?><td id="email_content" style="font-family:&#039;lucida grande&#039;,tahoma,verdana,arial,sans-serif;font-size:12px;padding:0px;"><?php
						?><table cellspacing="0" cellpadding="0" width="100%" border="0" style="border-collapse:collapse;width:100%;"><?php
							?><tr><?php
								?><td style="font-size:11px;font-family:LucidaGrande,tahoma,verdana,arial,sans-serif;padding:0;border-left:none;border-right:none;border-top:none;border-bottom:none;"><?php
									?><table cellspacing="0" cellpadding="0" width="100%" style="border-collapse:collapse;"><?php
										?><tr><?php
											?><td style="padding:0;width:100%;"><?php

												self::header();

											?></td><?php
										?></tr><?php
										?><tr><?php
											?><td style="padding:0;width:100%;padding-left:30px;"><?php
												?><table cellspacing="0" cellpadding="0" width="100%" style="border-collapse:collapse;"><?php
													?><td><?php

														self::table_linebreak();

															?><table cellspacing="0" cellpadding="0" style="width:calc(100% - 500px);" style="border-collapse:collapse;"><?php
																?><tr><?php
																	?><td align="left" id="body_container" style="background-color:#fff;display:block;border-radius:5px;-webkit-border-radius:5px;-moz-border-radius:5px;box-shadow:0 1px 1px rgba(0,0,0,.10);overflow:hidden;"><?php
																		?><table cellspacing="0" cellpadding="0" width="100%" style="border-collapse:collapse;"><?php
																			?><td><?php
	}

	public static function main( $mode = 'in' )
	{
		switch( $mode )
		{
			case 'in'  : echo "<table cellspacing='0' cellpadding='0' width='100%' bgcolor'#f5f6f7' style='border-collapse:collapse;'>"; break;
			case 'out' : echo "</table>"; break;
		}
	}

	public static function out()
	{
																						?></td><?php
																					?></table><?php
																				?></td><?php
																			?></tr><?php
																		?></table><?php

																	self::table_linebreak();

																?></td><?php
															?></table><?php
														?></td><?php
													?></tr><?php
													?><tr><?php
														?><td style="padding:0;width:100%;"><?php self::footer();?></td><?php
													?></tr><?php
												?></table><?php
											?></td><?php
										?></tr><?php
									?></table><?php
								?></td><?php
							?></tr><?php
						?></table><?php
					?></body><?php
				?></html><?php
			?></body><?php
		?></html><?php
	}



	// == Private methods

	private static function table_linebreak()
	{
		?><table cellspacing="0" cellpadding="0" width="100%" style="border-collapse:collapse;"><?php
			?><tbody><?php
				?><tr><?php
					?><td height="19">&nbsp;</td><?php
				?></tr><?php
			?></tbody><?php
		?></table><?php
	}

	private static function footer()
	{
		?><table cellspacing="0" cellpadding="0" width="100%" style="border-collapse:collapse;background:rgba(255,255,255,1);"><?php
			?><tr><?php
				?><td style="padding-left:30px;"><?php

						?><table cellspacing="0" cellpadding="0" width="730" style="border-collapse:collapse;"><?php
				      ?><tr><?php
								?><td><?php
									?><table cellspacing="0" cellpadding="0" width="730" border="0" id="footer" style="border-collapse:collapse;"><?php
										?><tr><?php
											?><td style="font-size:13px;font-family:Helvetica Neue,Helvetica,Lucida Grande,tahoma,verdana,arial,sans-serif;padding:18px 0;border-left:none;border-right:none;border-top:none;border-bottom:none;color:#7f7f7f;font-weight:400;line-height:20px;text-align:left;border:none;"><?php
												?>This email was sent to <a href="mailto:<?=self::$email;?>" style="color:#7f7f7f;text-decoration:none;font-family:Helvetica Neue,Helvetica,Lucida Grande,tahoma,verdana,arial,sans-serif;font-weight:bold;"><?=self::$email;?></a>.<?php
											?></td><?php
										?></tr><?php
									?></table><?php
								?></td><?php
							?></tr><?php
						?></table><?php

				?></td><?php
			?></tr><?php
		?></table><?php
	}
}
