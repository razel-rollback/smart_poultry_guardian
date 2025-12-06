# Smart Poultry Monitoring & Control System
## Complete Setup Guide

## 🎯 System Overview

This IoT system integrates Arduino hardware with a Laravel web application to monitor and control poultry farm equipment remotely.

### Features:
1. **Automated Feeder** - Schedule feeds or trigger manually
2. **Temperature Control** - Auto fan activation with threshold settings
3. **Light Control** - Remote LED control via website
4. **Real-time Monitoring** - Live sensor data display
5. **Historical Data** - Feed logs and temperature history

---

## 🔌 Hardware Components

### Required Parts:
- Arduino Uno/Mega
- DHT22 Temperature & Humidity Sensor
- 12V DC Fan
- NPN Transistor (TIP120 or similar)
- 1N4007 Diode
- Servo Motor (SG90 or similar)
- Photoresistor (LDR)
- Red LED
- 10kΩ Resistor (for photoresistor)
- 220Ω Resistor (for LED)
- 12V Power Adapter
- Breadboard & Jumper Wires

---

## 🔧 Arduino Wiring Diagram

### DHT22 Sensor
```
DHT22 Pin 1 (VCC)  → Arduino 5V
DHT22 Pin 2 (DATA) → Arduino Digital Pin 2
DHT22 Pin 4 (GND)  → Arduino GND
```

### DC Fan Circuit (with Transistor)
```
Fan (+) → 12V Power Supply (+)
Fan (-) → Transistor Collector
Transistor Base → 1kΩ Resistor → Arduino Digital Pin 3
Transistor Emitter → GND
Diode (1N4007) → Across Fan terminals (cathode to +)
12V Power GND → Arduino GND (common ground)
```

### LED with Photoresistor
```
Photoresistor:
  One end → Arduino 5V
  Other end → Arduino A0 + 10kΩ resistor to GND

LED:
  Anode → Arduino Digital Pin 4 → 220Ω resistor
  Cathode → GND
```

### Servo Motor (Feeder)
```
Servo Red Wire (VCC)    → Arduino 5V
Servo Brown Wire (GND)  → Arduino GND
Servo Orange Wire (PWM) → Arduino Digital Pin 9
```

---

## 💻 Software Setup

### 1. Database Setup

Navigate to Laravel directory and run migrations:
```bash
cd laravel_app
php artisan migrate
```

This creates the following tables:
- `feeder_schedules` - Scheduled feeding times
- `feeder_logs` - Feed history
- `feeder_commands` - Manual feed commands
- `temperature_settings` - Threshold & fan override
- `temperature_readings` - Sensor data (every 5 mins)
- `light_controls` - LED on/off state

### 2. Arduino Setup

1. Install required libraries in Arduino IDE:
   - DHT sensor library (by Adafruit)
   - Servo library (built-in)

2. Upload the sketch:
   - Open `arduino_code/smart_poultry.ino`
   - Select your board and port
   - Click Upload

3. Test Serial Monitor:
   - Open Serial Monitor (9600 baud)
   - You should see: "System Ready" and sensor data like "DATA:25.50,60.00"

### 3. Python Bridge Setup

1. Install dependencies:
```bash
cd python_bridge
pip install pyserial requests
```

2. Update serial port in `bridge.py`:
   - Mac: `/dev/cu.usbmodem*` or `/dev/tty.usbmodem*`
   - Linux: `/dev/ttyACM0` or `/dev/ttyUSB0`
   - Windows: `COM3`, `COM4`, etc.

3. Find your Arduino port:
```bash
# Mac/Linux
ls /dev/cu.* 

# Or use Arduino IDE: Tools > Port
```

### 4. Start the System

**Terminal 1 - Laravel Server:**
```bash
cd laravel_app
php artisan serve
```

**Terminal 2 - Python Bridge:**
```bash
cd python_bridge
python bridge.py
```

**Terminal 3 - Access Website:**
Open browser: `http://localhost:8000`

---

## 🌐 API Endpoints

### Feeder
- `GET /api/feeder/schedules` - List schedules
- `POST /api/feeder/schedules` - Add schedule
- `PUT /api/feeder/schedules/{id}` - Update schedule
- `DELETE /api/feeder/schedules/{id}` - Delete schedule
- `POST /api/feeder/feed-now` - Manual feed
- `GET /api/feeder/history` - Feed logs
- `GET /api/feeder/check-status` - Status (for Python)
- `POST /api/feeder/log` - Log feed (for Python)

### Temperature
- `GET /api/temperature/settings` - Get settings
- `PUT /api/temperature/settings` - Update threshold/override
- `GET /api/temperature/latest` - Latest reading
- `GET /api/temperature/history` - Historical data
- `POST /api/temperature/log` - Log reading (for Python)
- `GET /api/temperature/fan-status` - Fan status (for Python)

### Light
- `GET /api/light/status` - Get status
- `POST /api/light/toggle` - Toggle on/off
- `PUT /api/light/status` - Set status

---

## 🔄 System Communication Flow

```
┌─────────────┐         ┌──────────────┐         ┌─────────────┐
│   Arduino   │ Serial  │    Python    │  HTTP   │   Laravel   │
│  (Hardware) │ ←─────→ │    Bridge    │ ←─────→ │   (MySQL)   │
└─────────────┘  9600   └──────────────┘         └─────────────┘
      │                        │                         │
      │ Sensor Data            │ Every 1 sec:            │
      │ "DATA:28.5,60.0"       │ Check commands          │
      └────────────────────────┤                         │
                               │ Every 5 mins:           │
                               │ Log sensor data         │
                               └─────────────────────────┤
                                                         │
                                                 ┌───────▼────────┐
                                                 │   Web Browser  │
                                                 │   Dashboard    │
                                                 └────────────────┘
```

---

## 📊 Usage Guide

### Feeder Control
1. **Manual Feed**: Click "FEED NOW" button
2. **Schedule Feed**: Enter time (e.g., 08:00) and click "Add"
3. **Toggle Schedule**: Check/uncheck the "Active" box
4. **View History**: Recent feeds shown in panel

### Temperature Monitoring
1. **Set Threshold**: Enter temperature (e.g., 30°C) and click "Set"
2. **Manual Fan Override**: Toggle switch to force fan ON
3. **Auto Mode**: Fan turns on automatically when temp > threshold
4. **Real-time Display**: Temperature and humidity update every 5 seconds

### Light Control
1. Click the light bulb button to toggle
2. LED on Arduino will mirror the website state

---

## 🔍 Troubleshooting

### Arduino not detected
```bash
# Check connection
ls /dev/cu.* # Mac
ls /dev/tty* # Linux

# Verify in Python bridge.py:
SERIAL_PORT = '/dev/cu.usbmodem141011'  # Update this
```

### No sensor data
- Check DHT22 wiring
- Open Arduino Serial Monitor (9600 baud)
- Should see: "DATA:25.50,60.00" every 2 seconds

### Fan not working
- Verify 12V power supply is connected
- Check transistor wiring
- Ensure common ground between Arduino and 12V supply

### Website not loading
```bash
# Check Laravel is running
php artisan serve

# Check for errors
php artisan log:tail
```

### Python bridge errors
```bash
# Connection error?
pip install --upgrade pyserial requests

# Port permission (Linux/Mac)
sudo chmod 666 /dev/ttyACM0
```

---

## 🎨 Customization

### Change Sensor Log Interval
In `python_bridge/bridge.py`:
```python
SENSOR_LOG_INTERVAL = 300  # Change from 5 mins (300s) to desired seconds
```

### Change Feeder Servo Angles
In `arduino_code/smart_poultry.ino`:
```cpp
void activateFeeder() {
  feederServo.write(90);   // Open angle (adjust 0-180)
  delay(1000);             // Duration
  feederServo.write(0);    // Close angle
}
```

### Add Email Notifications
Install Laravel mail package and add to controllers:
```php
Mail::to('owner@example.com')->send(new FeedAlert());
```

---

## 📝 Database Schema

### feeder_schedules
- id, feed_time, is_active, timestamps

### feeder_logs
- id, trigger_type (manual/scheduled), schedule_id, fed_at, timestamps

### temperature_readings
- id, temperature, humidity, recorded_at, timestamps

### temperature_settings
- id, threshold_temperature, fan_override, timestamps

### light_controls
- id, is_on, timestamps

---

## 🚀 Advanced Features (Future)

- [ ] Mobile app with push notifications
- [ ] Water level monitoring
- [ ] Egg counter with IR sensor
- [ ] Camera livestream
- [ ] SMS alerts for critical temperatures
- [ ] Multi-coop management

---

## 📞 Support

For issues or questions:
1. Check Arduino Serial Monitor for hardware issues
2. Check Laravel logs: `laravel_app/storage/logs/laravel.log`
3. Check Python bridge console output

---

## 📄 License

MIT License - Free to use and modify
# smart_poultry_guardian
