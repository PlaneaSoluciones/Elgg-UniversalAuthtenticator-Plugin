<?php
/**
 * Universal Authtenticator
 *
 * @license GNU Public License version 3
 * @author Diego Capape <dcapape@planeasoluciones.com>
 * @link http://www.planeasoluciones.com
 */

// User Registration and Admin User Registration Event Handler
elgg_register_event_handler('register', 'user', 'sync_register', 400);
elgg_register_event_handler('create', 'user', 'sync_register', 400);
// User Delete Event Handler
elgg_register_event_handler('delete', 'user', 'sync_delete', 400);
// User Setttings Event Handler
elgg_register_event_handler('usersettings:save', 'user', 'sync_update', 400);
elgg_register_plugin_hook_handler('usersettings:save', 'user', 'sync_update', 400);
// User Change Password Event Handler
elgg_register_plugin_hook_handler('action', 'user/changepassword', 'sync_changepassword', 400);

function sync_register($hook, $entity_type, $returnvalue, $params) {
error_log(json_encode($hook));
error_log(json_encode($entity_type));
error_log(json_encode($returnvalue));
error_log(json_encode($params));
	$post = array(
		'action'   => 'create',
		'username' => get_input('username'),
		'password' => get_input('password'),
		'name'     => get_input('name'),
		'email'	   => get_input('email'),
	);

	$response = send_xmpp($post);

	if ($response['status'] == true)
		$response = send_owncloud($post);
	else
		return false;

	if ($response['status'] == true)
    return true;
  else
    return false;
}


function sync_delete($hook, $entity_type, $returnvalue, $params) {
	if ($hook == "delete" and $entity_type == "user"){

		$post = array(
			'action'   => 'delete',
			'username' => !empty(get_input('username')) ? get_input('username')  : get_user(get_input('guid'))->username
			//'username' => get_user(get_input('guid'))->username
		);

    $response = send_xmpp($post);

  	if ($response['status'] == true)
    	$response = send_owncloud($post);
    else
      return false;

    if ($response['status'] == true)
      return true;
    else
      return false;


	}
}



function sync_update($hook, $entity_type, $returnvalue, $params) {

	$post = array(
		'action'   => 'update',
								'username' => get_user(get_input('guid'))->username,
                'password' => get_input('password'),
                'name'     => get_input('name'),
                'email'    => get_input('email'),
	);

  $response = send_xmpp($post);

  if ($response['status'] == true)
    $response = send_owncloud($post);
  else
    return false;

  if ($response['status'] == true)
    return true;
  else
    return false;

}

function sync_changepassword ($hook, $entity_type, $returnvalue, $params) {

		$post = array(
			'action'   => 'update_password',
			'username' => get_user(get_input('u'))->username,
			'password' => get_input('password1')
		);

		if (get_input('password1') == get_input('password2')) {

			$response = send_xmpp($post);

			if ($response['status'] == true)
				$response = send_owncloud($post);
			else
        return false;

			if ($response['status'] == true)
        return true;
      else
        return false;
		}else{
			return false;
		}

}

 ?>
