#include <Wire.h>
#include <Adafruit_PN532.h>
#include <SPI.h>
#include <WiFi.h> // Menggunakan WiFi.h untuk ESP32
#include <HTTPClient.h> // Untuk HTTP Client di ESP32
#include <LiquidCrystal_I2C.h>

#define SCK_PIN   19  // SCK ke IO19
#define MISO_PIN  18  // MISO ke IO18
#define MOSI_PIN  23  // MOSI ke IO23
#define SS_PIN    5   // SS ke IO5

// Ganti dengan nama dan password Wi-Fi Anda
const char* ssid = "SARPRA";
const char* password = "itnusaputera";

// URL server tempat data akan dikirim
const char* serverUrl = "http://192.168.114.124/presensi_iot/test.php"; // Menghapus // ganda
LiquidCrystal_I2C lcd(0x27, 20, 4); // Ganti ke 0x3F jika tidak tampil
Adafruit_PN532 nfc(19, 18, 23, 5);  // Menggunakan SPI, SS ke pin 5

String getValue(String data, char separator, int index)
{
  int found = 0;
  int strIndex[] = {0, -1};
  int maxIndex = data.length()-1;

  for(int i=0; i<=maxIndex && found<=index; i++){
    if(data.charAt(i)==separator || i==maxIndex){
        found++;
        strIndex[0] = strIndex[1]+1;
        strIndex[1] = (i == maxIndex) ? i+1 : i;
    }
  }

  return found>index ? data.substring(strIndex[0], strIndex[1]) : "";
}

void sendUIDToServer(uint8_t *uid, byte uidSize) {
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;
    http.begin(serverUrl); // Inisialisasi URL server
    http.addHeader("Content-Type", "application/x-www-form-urlencoded");

    String uidHex = "";
    for (byte i = 0; i < uidSize; i++) {
      uidHex += String(uid[i], HEX);
    }
    uidHex.toUpperCase(); // Pastikan UID dikirim dalam huruf besar

    // Kirim UID dalam format hex
    String postData = "uid=" + uidHex;
    Serial.println("Sending UID: " + postData); // Debugging

    int httpCode = http.POST(postData); // Kirim data
    String responseMessage = "";
    String responseCode = "";
    if (httpCode > 0) {
      String payload = http.getString();
      Serial.println("Raw payload: " + payload);
      responseMessage = getValue(payload,'$',0);
      responseCode = getValue(payload,'$',1);
      Serial.println("Sent this: " + postData);
      Serial.println("Response: " + responseMessage); // Tampilkan respon server
      Serial.println("Response code: " + responseCode);

      if(responseCode == "1")
        {
          digitalWrite(27, HIGH);
          delay(500);
          digitalWrite(27, LOW);
          lcd.clear();
          lcd.setCursor(0, 0);
          lcd.print(String(responseMessage));
          delay(5000);
        } 
        else if (responseCode == "0")
        {
          digitalWrite(27, HIGH);
          delay(100);
          digitalWrite(27, LOW);
          delay(100);
          digitalWrite(27, HIGH);
          delay(100);
          digitalWrite(27, LOW);
          lcd.clear();
          lcd.setCursor(0, 0);
          lcd.print(String(responseMessage));
          delay(3000);
        }

    } else {
      Serial.println("Error in HTTP request: " + String(httpCode));
    }

    http.end(); // Menutup koneksi HTTP
  } else {
    Serial.println("WiFi Disconnected");
  }
}

void setup() {
  pinMode(27, OUTPUT);
    Serial.begin(115200);
    Serial.println("Initializing PN532...");

    // Inisialisasi PN532
    nfc.begin();
 nfc.setPassiveActivationRetries(0xff); //retry forever
    uint32_t version = nfc.getFirmwareVersion();
    if (!version) {
        Serial.println("Didn't find PN532 board");
        nfc.begin();
    }

    // Koneksi ke Wi-Fi
    WiFi.begin(ssid, password);
    Serial.print("Connecting to WiFi");
    while (WiFi.status() != WL_CONNECTED) {
        delay(500);
        Serial.print(".");
    }
    Serial.println("\nConnected to WiFi");

    Wire.begin(25, 26);  

      lcd.init();
      lcd.backlight();
      lcd.clear();
      lcd.setCursor(0, 0);
      lcd.print("RFID Presensi");
      lcd.setCursor(0, 1);
      lcd.print("Scan Kartu Anda...");
}

void loop() {

     uint32_t version = nfc.getFirmwareVersion();
    if (!version) {
        Serial.println("Waiting for NFC tag...");
        Serial.println("Didn't find PN532 board");
        nfc.begin();
        lcd.backlight();
        lcd.clear();
        lcd.setCursor(0, 0);
        lcd.print("RFID Presensi");
        lcd.setCursor(0, 1);
        lcd.print("Scan Kartu Anda...");
    }
        Serial.println(version);
    uint8_t success;
    uint8_t uid[] = {0}; // Buffer to store the returned UID
    uint8_t uidLength; // Length of the UID (4 or 7 bytes depending on ISO14443A card type)

    success = nfc.readPassiveTargetID(PN532_MIFARE_ISO14443A, uid, &uidLength);

    if (success) {
      digitalWrite(27, HIGH);
      delay(500);
      digitalWrite(27, LOW);
        Serial.print("Found NFC tag with UID: ");
        for (uint8_t i = 0; i < uidLength; i++) {
            Serial.print(uid[i], HEX);
            Serial.print(" ");
        }
        Serial.println();
        sendUIDToServer(uid, uidLength);
        
    }
    delay(1000);
}