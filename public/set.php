<?php

require_once("common.php");

$function = $_REQUEST["func"] ?? "";
$username = $_REQUEST["u"] ?? "";
$password = $_REQUEST["p"] ?? "";
$vlan = $_REQUEST["v"] ?? "";
$id = $_REQUEST["id"] ?? "";

if ($function == "new-user") {


	if (verify_input($username, $password, $vlan)) {
		$response = $unifi_connection->create_radius_account($username, $password, 13, 6, $vlan);
		if ($response) {
			header('HTTP/1.1 200 OK');
			echo json_encode($response, JSON_PRETTY_PRINT);
			die();
		}
	}
}
elseif ($function == "edit-user") {
	if (verify_input($username, $password, $vlan) && verify($id)) {
		$existing = $unifi_connection->list_radius_accounts();
		foreach ($existing as $e) {
			if ($e->_id == $id) {
				$e->name = $username;
				$e->x_password = $password;
				$e->vlan = $vlan;
				$response = $unifi_connection->set_radius_account_base($id, $e);

				if ($response) {
					header('HTTP/1.1 200 OK');
					echo json_encode($response, JSON_PRETTY_PRINT);
					die();
				}
			}
		}
	}
}
elseif ($function == "delete-user") {
	if (verify($id)) {
		if ($unifi_connection->delete_radius_account($id)) {
			header('HTTP/1.1 200 OK');
			die();
		}
	}
}
elseif ($function == "update-aps-in-group") {
	$name = $_REQUEST["name"] ?? "";
	if (verify($id) && verify($name)) {
		$aps = $_REQUEST["aps"];

		$ap_list = [];
		foreach ($aps as $ap) {
			if (preg_match("/(?:[a-z0-9]{2}:){5}[a-z0-9]{2}/", $ap["id"]) && $ap["checked"] == 1) {
				$ap_list[] = $ap["id"];
			}
		}
		$unifi_connection->edit_apgroup($id, $name, $ap_list);
		header('HTTP/1.1 200 OK');
		die();
	}
}
elseif ($function == "update-ssids-in-group") {
	$name = $_REQUEST["name"] ?? "";
	if (verify($id) && verify($name)) {
		$submitted_ssids = $_REQUEST["ssids"];
		$included_ssids = [];
		$excluded_ssids = [];
		foreach ($submitted_ssids as $ss) {
			if (ctype_print($ss["id"])) {
				if ($ss["checked"]) {
					$included_ssids[] = $ss["id"];
				}
				else {
					$excluded_ssids[] = $ss["id"];
				}
			}
		}
		$site_ssids = $unifi_connection->list_wlanconf();
		$changed_ssids = [];
		foreach ($site_ssids as $ssid) {
			if (in_array($ssid->_id, $included_ssids) && !in_array($id, $ssid->ap_group_ids)) {
				$ssid->ap_group_ids[] = $id;
				$changed_ssids[] = $ssid->_id;
			}
			elseif (in_array($ssid->_id, $excluded_ssids) && in_array($id, $ssid->ap_group_ids)) {
				$ssid->ap_group_ids = array_diff($ssid->ap_group_ids, [$id]);
				$changed_ssids[] = $ssid->_id;
			}
		}
		foreach ($site_ssids as $ssid) {
			if (in_array($ssid->_id, $changed_ssids)) {
				$result = $unifi_connection->set_wlansettings_base($ssid->_id, ["ap_group_ids" => $ssid->ap_group_ids]);
				if (!$result) {
					header("HTTP/1.1 400 Bad Request");
					echo json_encode(["error" => $unifi_connection->get_last_error_message()]);
					die();
				}
			}
		}
		header('HTTP/1.1 200 OK');
		die();
	}
}
elseif ($function == "rename-ap-group") {
	$new_name = $_REQUEST["n"] ?? null;
	if (verify($new_name) && verify($id)) {
		$ap_groups = $unifi_connection->list_apgroups();
		$macs = [];
		$found = false;
		foreach ($ap_groups as $g) {
			if ($g->_id == $id) {
				$macs = $g->device_macs;
				$found = true;
			}
		}
		if ($found) {
			$unifi_connection->edit_apgroup($id, $new_name, $macs);
			header('HTTP/1.1 200 OK');
			die();
		}
	}
}
elseif ($function == "wireless") {
	header("Content-Type: application/json");
	echo json_encode($unifi_connection->list_apgroups());
}

header('HTTP/1.1 400 Bad Request');


function verify_input($username, $password, $vlan) {
	return verify($username) && verify($password) && verify($vlan);
}

function verify($input) {
	return ctype_print($input);
}
