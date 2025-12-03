<?php
// Included for compatibility if you later want MQTT - currently not used.
// Minimal stub to avoid errors if referenced.
class phpMQTT {
    public function __construct($server, $port, $client_id) {}
    public function connect(){ return true; }
    public function publish($t,$m,$q,$retain=false) {}
    public function subscribe($topics,$q) {}
    public function proc(){ return false; }
    public function close() {}
}
?>
