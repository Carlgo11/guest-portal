---
layout: default
---
<?php
require_once __DIR__ . '/vendor/autoload.php';

$unifi_connection = new UniFi_API\Client($_ENV['hotspot_user'], $_ENV['hotspot_password'], $_ENV['unifi_url'], $_ENV['unifi_site'], $_ENV['unifi_version'], FALSE);
$login = $unifi_connection->login();
$guests = $unifi_connection->list_guests();
$clients = $unifi_connection->list_clients();
?>
<div class="main col-12 col-md-10 col-xl-5">
    <!-- Requests -->
    <div class="col-md-6 noselect" style="float:left;text-align: left;">
        <h1 style="text-align: center;">Requests</h1>
        <ul class="list-group">
            <?php foreach($clients as $client ) {
                $client = get_object_vars($client);
                if ($client['is_guest'] == null){
                    continue;
                }
                if($client['authorized']){
                    continue;
                }

            ?>
            <li class="list-group-item d-flex justify-content-between align-items-center"><?php echo $client['hostname']; ?>
                <div class="request-buttons">
                    <button class="btn btn-success"><i class="fas fa-check"></i></button>
                    <button class="btn btn-danger"><i class="fas fa-times"></i></button>
                </div>
            </li>
            <?php } ?>

        </ul>
    </div>
    <!-- Online users -->
    <div class="col-md-6 noselect" style="float:right; text-align: left;">
        <h1 style="text-align: center;">Online</h1>
        <ul class="list-group">
            <?php
            foreach($guests as $guest) {
                $guest = get_object_vars($guest);
                $mac = $guest['mac'];
                $name = $unifi_connection->list_devices($mac);

                if ($name != "") {
                    $output = $name;
                }
                else {
                    $output = $mac;
                }
            ?>
            <li class="list-group-item"><?php echo $output; ?></li>
            <?php
            }
            ?>
        </ul>
    </div>
</div>