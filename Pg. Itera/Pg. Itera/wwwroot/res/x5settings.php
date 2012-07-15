<?php

/************
	GENERAL SITE SETTINGS
************/

$imSettings['general'] = array(
	'dir' => '',
	'url' => 'http://www.itera.com.pe'
);

$imSettings['guestbook']['public_folders'] = array();

/************
	EMAIL SETTINGS
************/

$imSettings['email'] = array(
	'header' => '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">' . "\n" . '<html>' . "\n" . '<head>' . "\n" . '<meta http-equiv="content-type" content="text/html; charset=utf-8">' . "\n" . '<meta name="generator" content="Incomedia WebSite X5 v9 - www.websitex5.com">' . "\n" . '</head>' . "\n" . '<body bgcolor="#708090" style="background-color: #708090;">' . "\n\t" . '<table border="0" cellpadding="0" align="center" cellspacing="0" style="border-collapse: collapse; padding: 0; margin: 0 auto; width: 700px;">' . "\n\t" . '<tr><td id="imEmailContent" style="min-height: 300px; padding: 10px; font: normal normal normal 9.0pt Tahoma; color: #000000; background-color: #FFFFFF; text-align: left; text-decoration: none;  width: 700px;border-width: 1px 1px 0px 1px;border-style: solid; border-color: #808080; background-color: #FFFFFF" width="700px">' . "\n\t\t",
	'footer' => "\n\t" . '</td></tr>' . "\n\t" . '<tr><td id="imEmailFooter" style="font: normal normal normal 7.0pt Tahoma; color: #000000; background-color: transparent; text-align: center; text-decoration: none;  width: 700px;border-width: 0px 1px 1px 1px; border-style: solid; border-color: #808080;padding: 10px; background-color: #FFFFFF; " width="700px">' . "\n\t\t" . 'Este e-mail incluye información exclusiva para el destinatario mencionado anteriormente.<br>Si lo ha recibido por error, notifíqueselo inmediatamente al remitente y destrúyalo sin copiarlo.' . "\n\t" . '</td></tr>' . "\n\t" . '</table>' . "\n" . '</body>' . "\n" . '</html>',
	'body_background' => '#FFFFFF',
	'body_background_even' => '#FFFFFF',
	'body_background_odd' => '#F0F0F0',
	'body_background_border' => '#CDCDCD',
	'email_background' => '#708090',
	'email_content_style' => 'font: normal normal normal 9.0pt Tahoma; color: #000000; background-color: #FFFFFF; text-align: left; text-decoration: none; '
);

// End of file x5settings.php