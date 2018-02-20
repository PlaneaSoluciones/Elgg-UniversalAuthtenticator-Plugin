<?php
/**
 * Universal Authtenticator
 *
 * @license GNU Public License version 3
 * @author Diego Capape <dcapape@planeasoluciones.com>
 * @link http://www.planeasoluciones.com
 */

elgg_register_event_handler('init', 'system', 'ws_init_custom');

function ws_init_custom() {
	$lib_dir = elgg_get_plugins_path() . "web_services/lib";
	elgg_register_library('elgg:ws', "$lib_dir/web_services.php");
	elgg_register_library('elgg:ws:api_user', "$lib_dir/api_user.php");
	elgg_register_library('elgg:ws:client', "$lib_dir/client.php");
	elgg_register_library('elgg:ws:tokens', "$lib_dir/tokens.php");
	elgg_load_library('elgg:ws:api_user');
	elgg_load_library('elgg:ws:tokens');
	elgg_register_page_handler('services', 'ws_page_handler');

	elgg_ws_expose_function(
		"account.info",
		"account_info",
			[
				"username" =>
					[
						'type' => 'string',
						'required' => true
					]
			],
		'A testing method which echos back a string',
		'POST',
		true,
		false
	);

	elgg_ws_expose_function(
		"account.infobymail",
		"account_infobymail",
			[
			"email" =>
				[
					'type' => 'string',
					'required' => true
				]
			],
		'A testing method which echos back a string',
		'POST',
		true,
		false
	);

	elgg_ws_expose_function(
		"account.create",
		"account_create",
			[
			"username" =>
				[
					'type' => 'string',
					'required' => true,
				],
			"password" =>
				[
					'type' => 'string',
					'required' => true,
				],
			"name" =>
				[
					'type' => 'string',
					'required' => true,
				],
			"email" =>
				[
					'type' => 'string',
					'required' => true,
				],
			"validate" =>
				[
					'type' => 'boolean',
					'required' => true,
				]
			],
		'A method to create/register a new user',
		'POST',
		true,
		false
	);

	elgg_ws_expose_function(
		"account.validate",
		"account_validate",
			[
				"username" =>
					[
						'type' => 'string',
						'required' => true,
					]
			],
		'A method to create/register a new user',
		'POST',
		true,
		false
	);

	elgg_ws_expose_function(
		"account.delete",
		"account_delete",
			[
				"username" =>
					[
						'type' => 'string',
						'required' => true,
					],
			],
		'A method to delete an user',
		'POST',
		true,
		false
	);

	elgg_ws_expose_function(
		"account.ban",
		"account_ban",
			[
				"username" =>
					[
						'type' => 'string',
						'required' => true,
					],
			],
		'A method to ban an user',
		'POST',
		true,
		false
	);

	elgg_ws_expose_function(
		"account.update",
		"account_update",
			[
			"username" =>
				[
					'type' => 'string',
					'required' => true,
				],
			"password" =>
				[
					'type' => 'string',
					'required' => false,
				],
			"name" =>
				[
					'type' => 'string',
					'required' => false,
				],
			"email" =>
				[
					'type' => 'string',
					'required' => false,
				]
			],
		'A method to update an user information',
		'POST',
		true,
		false
	);
}

/**
* account.info method
*
* @param 	string $username Elgg Username
* @return string JSON Array. Status: 0 = Success, -1 = Error. Result: User Array
*
**/
function account_info($username) {
	error_log("PARTICIPA INFO BY USERNAME");
	error_log("Username: ".$username);
	error_log("----------------");
	$user = get_user_by_username($username);
	error_log("User Object:");
	error_log(json_encode($user));
	$validated = ($user->banned == "no")?true:false;
	error_log("Validate: ".$validated);
	return array(
		'guid' => $user->guid,
		'username' => $user->username,
		'name' => $user->name,
		'email' => $user->email,
		'validated' => $validated
	);
}

/**
* account.infobymail method
*
* @param 	string $email Elgg User E-mail
* @return string JSON Array. Status: 0 = Success, -1 = Error. Result: User Array
*
**/
function account_infobymail($email) {
	error_log("PARTICIPA INFO BY MAIL");
	error_log("Email: ".$email);
	error_log("----------------");
	try{
		$user = get_user_by_email($email);
	}catch (Exception $e){
		error_log($e->getMessage());
		return null;
	}
	error_log("User Object:");
	error_log(json_encode($user));
	if (!isset($user) || !array_key_exists(0,$user)){
		return false;
	}else{
		$user_array = array(
			'guid' => $user[0]->guid,
			'username' => $user[0]->username,
			'name' => $user[0]->name,
			'email' => $user[0]->email,
			'validated' => ($user[0]->banned == "no") ? true : false
		);
		error_log(json_encode($user_array));
		return $user_array;
	}
	return false;
}

/**
* account.create method
*
* @param 	string	 	$username New Username
* @param 	string 		$password New Plain Password
* @param 	string 		$name New Real Name
* @param 	string 		$email New email address
* @param 	boolean 	$validate 1=Validated 0=Not authorized
* @return string 		JSON Array. Status: 0 = Success, -1 = Error. Result: $guid (User Unique ID)
*
**/
function account_create($username, $password, $name, $email, $validate=true){
	error_log("PARTICIPA CREATE");
	error_log("Username: ".$username);
	error_log("Password: ".$password);
	error_log("Name: ".$name);
	error_log("Email: ".$email);
	error_log("Validate: ".json_encode($validate));
	error_log("----------------");

	$guid =	register_user($username, $password, $name, $email);
	error_log("User guid: ".$guid);
	if ($guid>0){
		$user_array = array(
			'guid' => $guid
		);
		if (!$validate){
			error_log("Applying Ban");
			//usleep(500000);
			$ban = ban_user($guid, "Created by Participa");
			if ($ban == true){
				error_log("Banned");
				return $user_array;
			}
		}
	}
	return $user_array;
}

/**
* account.validate method
*
* @param 	string $username Elgg Username
* @return string JSON Array. Status: 0 = Success, -1 = Error. Result: User Array
*
**/
function account_validate($username){
	error_log("PARTICIPA VALIDATE");
	error_log("Username: ".$username);
	error_log("----------------");
	$user = get_user_by_username($username);
	error_log("User Object:");
	error_log(json_encode($user));
	if ($user == null)
		return false;
	error_log("User Banned? ".$user->banned);
	error_log("User guid: " . $user->guid);
	$validated = ($user->banned == "no")?true:false;
	$user_array = array(
	  'guid' => $user->guid,
	  'username' => $user->username,
	  'name' => $user->name,
	  'email' => $user->email,
	  'validated' => $validated
	);
	/*if (unban_user($user->guid))
		return $user_array;
	else*/
	return $user_array;
}

/**
* account.delete method
*
* @param 	string $username Elgg Username
* @return string JSON Array. Status: 0 = Success, -1 = Error. Result: true = Success, false = Error
*
**/
function account_delete($username){
	error_log("PARTICIPA DELETE");
	error_log("Username: ".$username);
	error_log("----------------");
	$admin = get_user_by_username(elgg_admin);
	login($admin, true);
	$user = get_user_by_username($username);
	error_log("User Object:");
	error_log(json_encode($user));
	return $user->delete();
}

/**
* account.ban method
*
* @param 	string $username Elgg Username
* @return string JSON Array. Status: 0 = Success, -1 = Error. Result: true = Success, false = Error
*
**/
function account_ban($username){
	error_log("PARTICIPA DELETE");
	error_log("Username: ".$username);
	error_log("----------------");
	$admin = get_user_by_username(elgg_admin);
	login($admin, true);
	$user = get_user_by_username($username);
	error_log("User GUID:");
	error_log(json_encode($user->guid));
	return ban_user($user->guid, "Created by Participa");
}

/**
* account.update method
*
* @param 	string	 	$username User's Username to be updated
* @param 	string 		$password New password. If NULL, no changes
* @param 	string 		$name New name. If NULL, no changes
* @param 	string 		$email New email address. If NULL, no changes
* @return string 		JSON Array. Status: 0 = Success, -1 = Error. Result: boolean true = Success, false = Update Error
*
**/
function account_update($username, $password=null, $name=null, $email=null){
	error_log("PARTICIPA UPDATE");
	error_log("Username: ".$username);
	error_log("Password: ".$password);
	error_log("Name: ".$name);
	error_log("Email: ".$email);
	error_log("----------------");

	$admin = get_user_by_username(elgg_admin);
	login($admin, true);

	$user = get_user_by_username($username);
	$user->username = $username;

	error_log("User Object:");
	error_log(json_encode($user));

	if ($password != null)
		force_user_password_reset($user->guid, $password);
	if ($name != null)
		$user->name = $name;
	if ($email != null)
		$user->email = $email;

	$user->save();

	$post = array(
		'action'   => 'update',
		'username' => $username,
		'password' => $password,
		'name'     => $name,
		'email'    => $email,
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

?>
