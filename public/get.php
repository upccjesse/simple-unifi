<?php

require_once("common.php");



$function = $_REQUEST["func"] ?? "";
$id = $_REQUEST["id"] ?? "";

if ($function == "table") {
	$results = $unifi_connection->list_radius_accounts();
	$output = [];
	foreach ($results as $r) {
		switch ($r->tunnel_type) {
			case "3":
				$type = "L2TP";
				break;
			case "13":
				$type = "VLAN";
				break;
			default:
				$type = "Other";
		}
		$output[] = [
			$r->name,
			$r->x_password,
			$type,
			$r->vlan ?? "",
			$r->_id
		];
	}
	echo json_encode(["data" => $output]);
}
elseif ($function == "ap-group-list") {
	$results = $unifi_connection->list_apgroups();
	$ssids = $unifi_connection->list_wlanconf();
	$ssid_count = [];
	$output = [];
	foreach ($ssids as $s) {
		foreach ($s->ap_group_ids as $gid) {
			if (!array_key_exists($gid, $ssid_count)) {
				$ssid_count[$gid] = 0;
			}
			$ssid_count[$gid] += 1;
		}
	}
	foreach ($results as $g) {
		$output[] = [
			$g->name,
			count($g->device_macs),
			$ssid_count[$g->_id] ?? 0,
			$g->_id,
			$g->device_macs
		];
	}
	echo json_encode(["data" => $output]);
}
elseif ($function == "ap-group") {
	$output = [];
	$macs_in_group = [];
	$all_aps = $unifi_connection->list_devices();
	if (ctype_print($id)) {
		$ap_group = $unifi_connection->list_apgroups();
		$selected_group = null;
		foreach ($ap_group as $g) {
			if ($g->_id == $id) {
				$selected_group = $g;
			}
		}
		if ($ap_group) {
			foreach ($selected_group->device_macs as $mac) {
				$macs_in_group[] = $mac;
			}
		}
	}
	foreach ($all_aps as $ap) {
		if ($ap->type == "uap" || $ap->is_access_point) {
			$unique_essids = [];
			foreach($ap->vap_table as $vap) {
				if (!in_array($vap->essid, $unique_essids)) {
					$unique_essids[] = $vap->essid;
				}
			}
			$output[] = [
				in_array($ap->mac, $macs_in_group) ? 1 : 0,
				$ap->name,
				count($unique_essids),
				$ap->mac,
				$ap->_id
			];
		}
	}
	echo json_encode(["data" => $output]);
}
elseif ($function == "ssid") {
	$output = [];
	$results = $unifi_connection->list_wlanconf();
	foreach ($results as $ssid) {
		$output[] = [
			json_encode($ssid->ap_group_ids),
			$ssid->name,
			count($ssid->ap_group_ids),
			$ssid->_id
	];
	}

	echo json_encode(["data" => $output]);
}
elseif ($function == "wireless-uplink") {
	$result = $unifi_connection->list_settings();
	$enabled = false;
	foreach ($result as $r) {
		if ($r->key == "connectivity" && $r->enabled) {
			$enabled = true;
		}
	}
	echo json_encode(["enabled"=> $enabled], JSON_PRETTY_PRINT);
}
elseif ($function == "wireless") {
	$result = $unifi_connection->list_wlanconf("5909222ea11560b094e59c4c");
	header("Content-Type: application/json");
	echo json_encode($result, JSON_PRETTY_PRINT);
}