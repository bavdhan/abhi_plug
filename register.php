<?php
if (isset($_GET['ip'])) {
    file_put_contents("esp_ip.txt", $_GET['ip']);
    echo "ESP IP saved: " . $_GET['ip'];
} else {
    echo "No IP received.";
}
?>
