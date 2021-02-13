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
    print(json_encode(['error' => "Invalid mac address.", 'success' => FALSE], JSON_PRETTY_PRINT));
    return;
}

$unifi_connection = new UniFi_API\Client(getenv('UNIFI_USER'), getenv('UNIFI_PASSWORD'), getenv('UNIFI_URL'), getenv('UNIFI_SITE'), getenv('UNIFI_VERSION'), false);
$login = $unifi_connection->login();
if (!$login) {
    header('Status: 403');
    print(json_encode(['error' => "Couldn't authenticate to server.", 'success' => FALSE], JSON_PRETTY_PRINT));
}
$vouchers = $unifi_connection->stat_voucher();


if (!isset($code)) {
    header('Status: 400');
    print(json_encode(['error' => "No voucher code received.", 'success' => FALSE], JSON_PRETTY_PRINT));
}

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
        if ($authorized) {
            header('Status: 202');
            $unifi_connection->revoke_voucher($voucher['_id']);
        }
        else header('Status: 500');
        print(json_encode(['success' => $authorized]));
        return;
    }
}

header('Status: 400');
print(json_encode(['success' => FALSE, 'error' => 'Voucher code incorrect.'], JSON_PRETTY_PRINT));
 return;
