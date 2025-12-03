<?php
// Simple Core PHP controller for ESP8266 Webserver (no MQTT, no DB)
// Put this folder inside XAMPP's htdocs, e.g. C:\xampp\htdocs\smartplug
// Edit config.php to set $esp_ip to your ESP IP address (e.g., 192.168.1.45)

require_once 'config.php';

function esp_request($path) {
    global $esp_ip;
    $url = "http://{$esp_ip}/" . ltrim($path, '/');
    // try using file_get_contents with a short timeout, fallback to cURL
    $opts = array(
      'http'=>array(
        'method'=>"GET",
        'timeout'=>3 // seconds
      )
    );
    $context = stream_context_create($opts);
    $result = @file_get_contents($url, false, $context);
    if ($result === FALSE && function_exists('curl_version')) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3);
        $result = curl_exec($ch);
        curl_close($ch);
    }
    return $result;
}

$status = 'UNKNOWN';
if (isset($_GET['action'])) {
    if ($_GET['action'] === 'on') {
        $r = esp_request('on');
        $status = $r ? trim($r) : 'ERROR';
    } elseif ($_GET['action'] === 'off') {
        $r = esp_request('off');
        $status = $r ? trim($r) : 'ERROR';
    } else {
        $status = 'INVALID';
    }
} else {
    $r = esp_request('status');
    $status = $r ? trim($r) : 'UNKNOWN';
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Smart Plug Control - Core PHP</title>
    <style>
        body{ font-family: Arial, Helvetica, sans-serif; background:#f7f7f7; text-align:center; padding:50px; }
        .card{ background:white; padding:30px; border-radius:8px; display:inline-block; box-shadow:0 2px 8px rgba(0,0,0,0.1); }
        .status{ font-size:36px; margin:20px; font-weight:bold; }
        .on{ color:green; }
        .off{ color:red; }
        .btn{ padding:12px 30px; font-size:18px; margin:8px; text-decoration:none; border-radius:6px; color:white; }
        .btn-on{ background:#28a745; }
        .btn-off{ background:#dc3545; }
        .small{ font-size:14px; color:#666; margin-top:12px; }
    </style>
</head>
<body>
<div class="card">
    <h2>Smart Plug - Core PHP Control</h2>
    <div class="status <?php echo strtolower($status); ?>">
        <?php echo htmlspecialchars($status); ?>
    </div>
    <div>
        <a class="btn btn-on" href="?action=on">TURN ON</a>
        <a class="btn btn-off" href="?action=off">TURN OFF</a>
        <a class="btn" style="background:#6c757d" href="?">REFRESH</a>
    </div>
    <div class="small">ESP IP: <?php echo htmlspecialchars($esp_ip); ?> — PHP server must reach ESP on local network.</div>
</div>
</body>
</html>
