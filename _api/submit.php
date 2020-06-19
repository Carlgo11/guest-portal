<?php

require_once __DIR__ . '/vendor/autoload.php';

$mac = filter_input(INPUT_POST, 'id');
$ap_mac = filter_input(INPUT_POST, 'ap');
$time = filter_input(INPUT_POST, 't');
$url = filter_input(INPUT_POST, 'url', FILTER_SANITIZE_URL);
$ssid = filter_input(INPUT_POST, 'ssid');
$code = str_replace('-', '', filter_input(INPUT_POST, 'code', FILTER_SANITIZE_NUMBER_INT));

if (!filter_var($mac, FILTER_VALIDATE_MAC) || !filter_var($ap_mac, FILTER_VALIDATE_MAC)) {
    header('Status: 400');
    print(json_encode(['error' => "Invalid mac address", 'success' => FALSE], JSON_PRETTY_PRINT));
    return;
}

$unifi_connection = new UniFi_API\Client($_ENV['hotspot_user'], $_ENV['hotspot_password'], $_ENV['unifi_url'], $_ENV['unifi_site'], $_ENV['unifi_version'], FALSE);
$login = $unifi_connection->login();
$vouchers = $unifi_connection->stat_voucher();

if (isset($code)) {
    foreach ($vouchers as $voucher) {
        $voucher = get_object_vars($voucher);
        if ($voucher['code'] == $code) {
            $max_up = NULL;
            $max_down = NULL;
            $usage_quota = NULL;
            if (isset($voucher['qos_rate_max_up'])) $max_up = $voucher['qos_rate_max_up'];
            if (isset($voucher['qos_rate_max_down'])) $max_down = $voucher['qos_rate_max_down'];
            if (isset($voucher['qos_usage_quota'])) $usage_quota = $voucher['qos_usage_quota'];

            $authorized = $unifi_connection->authorize_guest($mac, $voucher['duration'], $max_up, $max_down, $usage_quota, $ap_mac);
            $unifi_connection->revoke_voucher($voucher['_id']);
            header('Status: 202');
            print(json_encode(['success' => TRUE], JSON_PRETTY_PRINT));
            return;
        }
    }
}

header('Status: 400');
print(json_encode(['success' => FALSE, 'error' => 'Voucher code not found'], JSON_PRETTY_PRINT));
return;
