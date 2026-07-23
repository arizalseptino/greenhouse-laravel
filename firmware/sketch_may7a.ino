#include <WiFiClientSecure.h>
#include <WiFi.h>
#include <HTTPClient.h>
#include <DHT.h>
#include <Wire.h>
#include <LiquidCrystal_I2C.h>
#include <SPI.h>
#include <SD.h>
#include <ArduinoJson.h>
#include <RTClib.h>
#include <time.h>

#define DHT_PIN 4
#define SOIL_PIN 33
#define SD_CS 27
#define SD_SCK 18
#define SD_MOSI 23
#define SD_MISO 19

const char* WIFI_SSID = "rizal";
const char* WIFI_PASS = "12345678";
const char* SERVER_URL = "https://greenhouse-vokasiipb.up.railway.app/api/sensor-data";

const unsigned long SEND_INTERVAL = 300000; 
const unsigned long LCD_UPDATE = 3000;      
const unsigned long WIFI_RETRY = 30000;    

DHT dht(DHT_PIN, DHT22);
LiquidCrystal_I2C lcd(0x27, 16, 2);
RTC_DS3231 rtc;

float tempC = 0, humidity = 0;
int soilRaw = 0;
float soilPercent = 0;

unsigned long lastSend = 0;
unsigned long lastLCD = 0;
unsigned long lastRetry = 0;

bool sdOK = false;
bool wifiOK = false;
bool rtcOK = false;

int offlineCount = 0;

void updateLCD();
void initSD();
void countOffline();
String getTime();
void printTime(DateTime dt);
void sendData();
void saveSD(String status);
void reconnectWiFi();
void flushOfflineData();
void syncRTCFromNTP();

void setup() {
    Serial.begin(115200);
    delay(500);
    Serial.println("\n[GREENHOUSE SYSTEM FINAL NTP+RTC]\n");

    Wire.begin(21, 22);

    lcd.init();
    lcd.backlight();
    lcd.clear();
    lcd.print("Init System...");

    rtcOK = rtc.begin();

    Serial.print("RTC: ");
    if (rtcOK) {
        Serial.print("OK @ ");
        printTime(rtc.now());
        Serial.println();
    } else {
        Serial.println("FAIL");
    }

    dht.begin();

    SPI.begin(SD_SCK, SD_MISO, SD_MOSI, SD_CS);
    sdOK = SD.begin(SD_CS);

    Serial.print("SD: ");
    Serial.println(sdOK ? "OK" : "FAIL");

    if (sdOK) initSD();

    WiFi.mode(WIFI_STA);
    WiFi.begin(WIFI_SSID, WIFI_PASS);

    Serial.print("WiFi: Connecting");
    lcd.setCursor(0, 1);
    lcd.print("WiFi...");

    int tries = 0;
    while (WiFi.status() != WL_CONNECTED && tries++ < 20) {
        delay(500);
        Serial.print(".");
    }
    Serial.println();

    wifiOK = (WiFi.status() == WL_CONNECTED);

    if (wifiOK) {
        Serial.print("WiFi OK @ ");
        Serial.println(WiFi.localIP());

        if (rtcOK) {
            syncRTCFromNTP();
        }
    } else {
        Serial.println("WiFi FAIL");
    }

    lcd.clear();
    lcd.print("System Ready");
    delay(2000);
}

void loop() {
    unsigned long now = millis();

    float t = dht.readTemperature();
    float h = dht.readHumidity();

    if (!isnan(t)) tempC = t;
    if (!isnan(h)) humidity = h;

    soilRaw = analogRead(SOIL_PIN);
    soilPercent = ((float)soilRaw - 3161.74) / -24.96;
    if (soilPercent < 0) soilPercent = 0;
    if (soilPercent > 100) soilPercent = 100;

    if (now - lastLCD >= LCD_UPDATE) {
        updateLCD();
        lastLCD = now;
    }

    if (now - lastSend >= SEND_INTERVAL) {
        sendData();
        lastSend = now;
        
        if (wifiOK) {
            countOffline(); 
            if (offlineCount > 0) {
                flushOfflineData();
            }
        }
    }

    if (!wifiOK && now - lastRetry >= WIFI_RETRY) {
        reconnectWiFi();
        lastRetry = now;
    }

    delay(500);
}

void updateLCD() {
    static bool page = false;
    DateTime dt = rtc.now();

    lcd.clear();

    if (!page) {
        lcd.setCursor(0, 0);
        lcd.print("T:");
        lcd.print(tempC, 1);
        lcd.print(" H:");
        lcd.print((int)humidity);

        lcd.setCursor(0, 1);
        lcd.print("S:"); lcd.print(soilRaw);
        lcd.print(" P:"); lcd.print((int)soilPercent); 
        lcd.print("%");
    } else {
        lcd.setCursor(0, 0);
        lcd.print("WiFi:");
        lcd.print(wifiOK ? "OK" : "NO");
        lcd.print(" SD:");
        lcd.print(sdOK ? "OK" : "NO");

        lcd.setCursor(0, 1);

        char buf[17];
        sprintf(buf, "%02d/%02d %02d:%02d:%02d",
                dt.day(),
                dt.month(),
                dt.hour(),
                dt.minute(),
                dt.second());

        lcd.print(buf);
    }

    page = !page;
}

void syncRTCFromNTP() {
    Serial.println("Syncing NTP...");

    configTime(7 * 3600, 0, "pool.ntp.org", "time.nist.gov");

    struct tm timeinfo;
    int retry = 0;

    while (!getLocalTime(&timeinfo) && retry < 10) {
        delay(1000);
        Serial.print(".");
        retry++;
    }

    Serial.println();

    if (retry < 10) {
        rtc.adjust(DateTime(
            timeinfo.tm_year + 1900,
            timeinfo.tm_mon + 1,
            timeinfo.tm_mday,
            timeinfo.tm_hour,
            timeinfo.tm_min,
            timeinfo.tm_sec
        ));

        Serial.print("RTC synced: ");
        printTime(rtc.now());
        Serial.println();
    } else {
        Serial.println("NTP failed, use RTC backup");
    }
}

String getTime() {
    if (!rtcOK) return "NO_RTC";

    DateTime dt = rtc.now();

    char buf[20];
    sprintf(buf, "%04d-%02d-%02d %02d:%02d:%02d",
            dt.year(),
            dt.month(),
            dt.day(),
            dt.hour(),
            dt.minute(),
            dt.second());

    return String(buf);
}

void printTime(DateTime dt) {
    Serial.print(dt.year());
    Serial.print("-");
    Serial.print(dt.month());
    Serial.print("-");
    Serial.print(dt.day());
    Serial.print(" ");
    Serial.print(dt.hour());
    Serial.print(":");
    Serial.print(dt.minute());
    Serial.print(":");
    Serial.print(dt.second());
}

void initSD() {
    if (!SD.exists("/data.csv")) {
        File f = SD.open("/data.csv", FILE_WRITE);
        if (f) {
            f.println("timestamp,temp,humidity,soil_raw,soil_percent,status");
            f.close();
        }
    }

    countOffline();
}

void countOffline() {
    File f = SD.open("/data.csv");
    if (!f) return;

    offlineCount = 0;

    while (f.available()) {
        if (f.readStringUntil('\n').indexOf("OFFLINE") != -1) {
            offlineCount++;
        }
    }

    f.close();
}

void saveSD(String status) {
    if (!sdOK) return;
    File f = SD.open("/data.csv", FILE_APPEND);
    if (!f) return;

    f.print(getTime());
    f.print(",");
    f.print(tempC, 2);
    f.print(",");
    f.print(humidity, 2);
    f.print(",");
    f.print(soilRaw);      
    f.print(",");
    f.print(soilPercent, 2); 
    f.print(",");
    f.println(status);
    f.close();
}

void sendData() {
    Serial.print("[SEND] ");
    Serial.println(getTime());

    wifiOK = (WiFi.status() == WL_CONNECTED);

    if (!wifiOK) {
        Serial.println("OFFLINE -> saved to SD");
        saveSD("OFFLINE");
        return;
    }

    WiFiClientSecure client;
    client.setInsecure();

    HTTPClient http;
    http.begin(client, SERVER_URL);
    http.addHeader("Content-Type", "application/json");

    StaticJsonDocument<256> doc;
    doc["timestamp"] = getTime();
    doc["suhu_udara"] = tempC;
    doc["kelembaban_udara"] = humidity;
    doc["soil_analog"] = soilRaw;
    doc["kelembaban_tanah"] = soilPercent;

    String json;
    serializeJson(doc, json);

    int code = http.POST(json);

    Serial.print("HTTP CODE: ");
    Serial.println(code);

    http.end();

    if (code == 200 || code == 201) {
        Serial.println("SUCCESS");
        saveSD("SUCCESS");
    } else {
        Serial.println("FAILED -> SD");
        saveSD("OFFLINE");
    }
}

void flushOfflineData() {
    if (!sdOK || !wifiOK) return;

    Serial.println("[SD] Memulai proses flushing data offline...");
    File f = SD.open("/data.csv", FILE_READ);
    if (!f) return;

    while (f.available()) {
        String line = f.readStringUntil('\n');
        if (line.indexOf("OFFLINE") != -1) {
            int comma1 = line.indexOf(',');
            int comma2 = line.indexOf(',', comma1 + 1);
            int comma3 = line.indexOf(',', comma2 + 1);
            int comma4 = line.indexOf(',', comma3 + 1);
            int comma5 = line.indexOf(',', comma4 + 1);

            String ts = line.substring(0, comma1);
            float t = line.substring(comma1 + 1, comma2).toFloat();
            float h = line.substring(comma2 + 1, comma3).toFloat();
            int sa = line.substring(comma3 + 1, comma4).toInt();
            float sp = line.substring(comma4 + 1, comma5).toFloat();

            WiFiClientSecure client;
            client.setInsecure();
            HTTPClient http;
            http.begin(client, SERVER_URL);
            http.addHeader("Content-Type", "application/json");

            StaticJsonDocument<256> doc;
            doc["timestamp"] = ts;
            doc["suhu_udara"] = t;
            doc["kelembaban_udara"] = h;
            doc["soil_analog"] = sa;
            doc["kelembaban_tanah"] = sp;

            String json;
            serializeJson(doc, json);
            int code = http.POST(json);
            http.end();

            if (code == 200 || code == 201) {
                Serial.print("Flush Success: "); Serial.println(ts);
            } else {
                Serial.println("Flush Failed, stop processing.");
                break; 
            }
        }
    }
    f.close();
    
    SD.remove("/data.csv");
    initSD(); 
}


void reconnectWiFi() {
    Serial.println("[WiFi] reconnecting...");

    WiFi.begin(WIFI_SSID, WIFI_PASS);

    int tries = 0;
    while (WiFi.status() != WL_CONNECTED && tries++ < 20) {
        delay(500);
        Serial.print(".");
    }

    Serial.println();

    wifiOK = (WiFi.status() == WL_CONNECTED);

    if (wifiOK) {
        Serial.println("WiFi reconnected");

        if (rtcOK) {syncRTCFromNTP(); }

        flushOfflineData();

    } else {
        Serial.println("Reconnect failed");
    }
}