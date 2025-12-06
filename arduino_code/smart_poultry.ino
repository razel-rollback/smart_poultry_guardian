/*
 * ========================================
 * SMART POULTRY SYSTEM - ARDUINO
 * Update: Simplified "Dark = LED ON" Logic
 * ========================================
 */

#include <DHT.h>
#include <Servo.h>

// ========== PIN DEFINITIONS ==========
#define DHTPIN 8
#define DHTTYPE DHT22

#define FAN_PIN 9
#define LED_PIN 12
#define PHOTORESISTOR_PIN A0
#define SERVO_PIN 10

// ========== SETTINGS ==========
// Adjust this number to change sensitivity.
// 0 = Pitch Black, 1023 = Direct Sunlight.
// < 300 is usually a good "dusk/dark" starting point.
const int DARK_THRESHOLD = 300;   // Turn ON if reading is below this
const int BRIGHT_THRESHOLD = 400; // Turn OFF if reading is above this 

// ========== OBJECTS ==========
DHT dht(DHTPIN, DHTTYPE);
Servo feederServo;

// ========== TIMING & STATE ==========
unsigned long lastSensorRead = 0;
const long sensorInterval = 2000;

bool fanState = false;
bool ledState = false; 
bool manualLightMode = false;

void setup() {
  Serial.begin(9600);
  dht.begin();
  
  feederServo.attach(SERVO_PIN);
  feederServo.write(0);
  delay(500);
  feederServo.detach();
  
  pinMode(FAN_PIN, OUTPUT);
  pinMode(LED_PIN, OUTPUT);
  pinMode(PHOTORESISTOR_PIN, INPUT);
  
  digitalWrite(FAN_PIN, LOW);
  digitalWrite(LED_PIN, LOW);
  
  Serial.println("System Ready");
  delay(2000);
}

void loop() {
  // Read the Light Level (0 to 1023)
  int lightLevel = analogRead(PHOTORESISTOR_PIN);

  // ==================================================
  // 1. AUTOMATIC NIGHT LIGHT LOGIC (UPDATED)
  // ==================================================
  if (!manualLightMode) {
    // AUTOMATIC MODE: Use photoresistor
    // If the sensor value is LOWER than threshold, it is DARK -> Turn LED ON
    if (lightLevel < DARK_THRESHOLD) {
      if (!ledState) {
        digitalWrite(LED_PIN, HIGH);
        ledState = true;
      }
    }
    // Otherwise (it is bright) -> Turn LED OFF
    else if (lightLevel > BRIGHT_THRESHOLD) {
      if (ledState) {
        digitalWrite(LED_PIN, LOW);
        ledState = false;
      }
    }
  }

  // ==================================================
  // 2. SEND SENSOR DATA (Temp, Humidity, Light)
  // ==================================================
  unsigned long currentMillis = millis();
  
  if (currentMillis - lastSensorRead >= sensorInterval) {
    lastSensorRead = currentMillis;
    
    float humidity = dht.readHumidity();
    float temperature = dht.readTemperature();
    
    if (!isnan(humidity) && !isnan(temperature)) {
      // Format: DATA:temp,humidity,light
      Serial.print("DATA:");
      Serial.print(temperature, 2);
      Serial.print(",");
      Serial.print(humidity, 2);
      Serial.print(",");
      Serial.println(lightLevel); 
    }
  }
  
  // ==================================================
  // 3. CHECK COMMANDS
  // ==================================================
  if (Serial.available() > 0) {
    char command = Serial.read();
    switch (command) {
      case 'F': 
        activateFeeder(); 
        break;
        
      case 'H': 
        fanState = true; 
        digitalWrite(FAN_PIN, HIGH); 
        Serial.println("STATUS:Fan ON"); 
        break;
        
      case 'L': 
        fanState = false; 
        digitalWrite(FAN_PIN, LOW); 
        Serial.println("STATUS:Fan OFF"); 
        break;
        
      case 'N': 
        // Turn light ON and switch to MANUAL mode
        manualLightMode = true; 
        ledState = true; 
        digitalWrite(LED_PIN, HIGH); 
        Serial.println("STATUS:Light Manual ON"); 
        break;
        
      case 'M': 
        // Turn light OFF and RETURN to AUTO mode
        manualLightMode = false; 
        ledState = false; 
        digitalWrite(LED_PIN, LOW); 
        Serial.println("STATUS:Light Manual OFF -> Auto Mode"); 
        break;
        
      case 'A': 
        // Reset to automatic mode
        manualLightMode = false; 
        Serial.println("STATUS:Auto Light Mode"); 
        break;
    }
  }
}

void activateFeeder() {
  Serial.println("STATUS:Feeding...");
  feederServo.attach(SERVO_PIN);
  feederServo.write(90);
  delay(1000);                   
  feederServo.write(0);
  delay(500);
  feederServo.detach();
  Serial.println("STATUS:Feed complete");
}