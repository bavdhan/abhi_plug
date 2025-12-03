#include <ESP8266WiFi.h>
#include <ESP8266WebServer.h>
#include <WiFiClient.h>

const char* WIFI_SSID = "viia";
const char* WIFI_PASS = "12345678";

ESP8266WebServer server(80);

#define RELAY_PIN 14
#define LED_PIN   LED_BUILTIN

String currentState = "OFF";

void handleRoot() {
  String html = "<html><head><title>ESP8266 Relay</title></head><body>";
  html += "<h2>ESP8266 Relay Control</h2>";
  html += "<p>State: " + currentState + "</p>";
  html += "<p><a href=\"/on\"><button style='padding:10px 20px'>TURN ON</button></a> ";
  html += "<a href=\"/off\"><button style='padding:10px 20px'>TURN OFF</button></a></p>";
  html += "</body></html>";
  server.send(200, "text/html", html);
}

void sendStatus() {
  server.send(200, "text/plain", currentState);
}

void handleOn() {
  digitalWrite(RELAY_PIN, LOW);
  digitalWrite(LED_PIN, LOW);
  currentState = "ON";
  Serial.println("Relay: ON");
  sendStatus();
}

void handleOff() {
  digitalWrite(RELAY_PIN, HIGH);
  digitalWrite(LED_PIN, HIGH);
  currentState = "OFF";
  Serial.println("Relay: OFF");
  sendStatus();
}

void setup() {
  Serial.begin(115200);
  pinMode(RELAY_PIN, OUTPUT);
  pinMode(LED_PIN, OUTPUT);

  digitalWrite(RELAY_PIN, HIGH);
  digitalWrite(LED_PIN, HIGH);

  WiFi.begin(WIFI_SSID, WIFI_PASS);

  Serial.print("Connecting to WiFi");
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  Serial.println();

  Serial.print("Connected! IP: ");
  Serial.println(WiFi.localIP());

  // ⭐ AUTO SEND ESP IP TO PHP SERVER
  WiFiClient client;
  if (client.connect("10.56.231.101", 80)) {     // CHANGE THIS → XAMPP IP
    client.print(String("GET ") + "/smartplug/register.php?ip=" + WiFi.localIP().toString() + " HTTP/1.1\r\n" +
                 "Host: 10.56.231.101\r\n" +
                 "Connection: close\r\n\r\n");
    client.stop();
  }

  server.on("/", handleRoot);
  server.on("/on", handleOn);
  server.on("/off", handleOff);
  server.on("/status", sendStatus);

  server.begin();
}

void loop() {
  server.handleClient();
}
