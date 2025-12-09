/*
 * ========================================
 * SMART POULTRY SYSTEM - ARDUINO
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
// Your photoresistor: HIGH value (600-700) = BRIGHT, LOW value (0-300) = DARK
const int BRIGHT_THRESHOLD = 500; // Turn OFF if reading is ABOVE this (it's bright)
const int DARK_THRESHOLD = 400;   // Turn ON if reading is BELOW this (it's dark) 

// ========== OBJECTS ==========
DHT dht(DHTPIN, DHTTYPE);
Servo feederServo;

// ========== TIMING & STATE ==========
unsigned long lastSensorRead = 0;
const long sensorInterval = 3000; 

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

  // LED is controlled by Python commands (N=ON, M=OFF)
  // Just like the fan is controlled by commands (H=ON, L=OFF)

  // ==================================================
  // 2. SEND SENSOR DATA (Temp, Humidity, Light)
  // ==================================================
  unsigned long currentMillis = millis();
  
  if (currentMillis - lastSensorRead >= sensorInterval) {
    lastSensorRead = currentMillis;
    
    float humidity = dht.readHumidity();
    float temperature = dht.readTemperature();
    
    if (!isnan(humidity) && !isnan(temperature)) {
      // Format: DATA:temp,humidity,light,ledState
      Serial.print("DATA:");
      Serial.print(temperature, 2);
      Serial.print(",");
      Serial.print(humidity, 2);
      Serial.print(",");
      Serial.print(lightLevel);
      Serial.print(",");
      Serial.println(ledState ? 1 : 0);  // Send LED state (1 = ON, 0 = OFF)
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
        ledState = true; 
        digitalWrite(LED_PIN, HIGH); 
        Serial.println("STATUS:LED ON"); 
        break;
        
      case 'M': 
        ledState = false; 
        digitalWrite(LED_PIN, LOW); 
        Serial.println("STATUS:LED OFF"); 
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