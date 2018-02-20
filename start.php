<?php
 /**
  * Universal Authtenticator
  *
  * @license GNU Public License version 3
  * @author Diego Capape <dcapape@planeasoluciones.com>
  * @link http://www.planeasoluciones.com
  */

elgg_register_event_handler('init', 'system', 'auth_init');
elgg_register_event_handler('init', 'system', 'ws_init');

include "config.php";
include "restapi.php";
include "handlers.php";


function send_xmpp($post){
	$ch = curl_init(xmpp_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
  $response = json_decode(curl_exec($ch), true);
	curl_close($ch);
	return $response;
}

function send_owncloud($post){
	$ch = curl_init(owncloud_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
  $response = json_decode(curl_exec($ch), true);
  curl_close($ch);
  return $response;
}
?>
